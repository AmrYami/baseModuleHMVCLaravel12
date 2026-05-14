---
name: madrasty-listing-rules
description: Enforces the sale/exchange/donation/wanted business rules and the listing status state machine. Trigger when editing listing controllers, services, models, requests, policies, or migrations that touch listings.listing_type / status / price / exchange_for_text / reserved_until.
---

# Madrasty Listing Rules

Madrasty has **four listing types** — each with its own required-fields and status flow:

| `listing_type` | Required fields | Forbidden fields | Closing status |
|---|---|---|---|
| `sale` | `price > 0` | — | `sold` |
| `exchange` | `exchange_for_text` (≤500 chars) | `price` (must be null/0 in API output) | `exchanged` |
| `donation` | — | `price` (must be null OR exactly 0) | `donated` |
| `wanted` | (no photo required) | `price`, `exchange_for_text` | `completed` (when matched) or `expired` |

Plus a **status state machine** that's owner-gated for happy-path transitions and admin-gated for rejections.

This skill loads whenever you touch listing-related code. **Every rule below is a hard line — do not weaken it with "for now" / "MVP" exceptions.**

---

## Inviolable rules

### 1. Type-specific field validation lives in FormRequest, not the controller

`StoreListingRequest` / `UpdateListingRequest` use Laravel's conditional rules:

```php
'price' => ['required_if:listing_type,sale', 'nullable', 'numeric', 'min:1'],
'exchange_for_text' => ['required_if:listing_type,exchange', 'nullable', 'string', 'max:500'],
```

PLUS a `withValidator(after)` step that enforces the **forbidden** sides:

- `donation`: if `price` is present and not 0, reject.
- `exchange`: if `price` is present, reject.
- `wanted`: if `price` or `exchange_for_text` is present, reject.
- `sale`: if `price` is missing or ≤ 0, reject.

Never put this logic in the controller body or the Service — it has to fail at the FormRequest layer so 422 fires before any DB write.

### 2. The closing status is determined by the listing_type, not by the caller

`POST /api/v1/listings/{id}/mark-completed` is **forbidden**. There is no generic "completed" endpoint. There are exactly:

- `mark-reserved` — any owner-active listing → `reserved` + `reserved_until = now() + 48h`.
- `mark-sold` — owner of `sale`/`reserved-from-sale` listing only.
- `mark-exchanged` — owner of `exchange`/`reserved-from-exchange` listing only.
- `mark-donated` — owner of `donation`/`reserved-from-donation` listing only.
- `cancel-reservation` — owner only, only when status is `reserved`.

Returning the wrong closing status from the wrong endpoint corrupts analytics and breaks the `wanted`-listing matching flow.

### 3. Owner-only mutations

`update`, `destroy`, `mark-reserved`, `mark-sold`, `mark-exchanged`, `mark-donated`, `cancel-reservation`, `bump`, `renew`, photo upload/delete — all owner-gated via `ListingPolicy`. Admin/moderator can ALSO `reject` and `hide`, but only the OWNER can mark sold/exchanged/donated/reserved.

### 4. Status state machine

Allowed transitions only:

```text
draft        → pending_review | deleted
pending_review → active | rejected | deleted
active       → reserved | sold | exchanged | donated | expired | rejected | deleted
reserved     → active (cancel) | sold | exchanged | donated | expired
sold | exchanged | donated | completed | rejected | expired | deleted → (terminal; only `renew` from expired)
```

Any other transition is a 422. Implement this as a `ListingStatusTransition` enum or a small array map; do NOT scatter `if ($listing->status === 'X')` checks across controllers.

### 5. Reservation TTL is automatic

When `mark-reserved` runs, set `reserved_until = now() + config('madrasty.listings.reservation_hours', 48)`. An hourly scheduled job (`PruneExpiredReservations`) auto-reverts `reserved` → `active` for any listing where `reserved_until < now()` and notifies both parties.

The admin/moderator should NEVER set `reserved_until` directly — it's computed.

### 6. Donation price serialization

Output: `price` must be `null` for `donation` and `exchange` listings in API responses. Even if the DB stores 0, the `ListingResource` must convert 0 → null when `listing_type !== 'sale'`. This avoids "free listing" rendering as "0 EGP" on Flutter.

### 7. `wanted` matching must not leak buyer info

The matching observer that fires when a new sale/exchange/donation matches a `wanted` listing notifies the wanted's owner by `notification`, not by exposing the new listing's seller's contact. The seller still has to accept a contact request.

### 8. Cross-skill: location chain still applies

Every listing write goes through `LocationValidationService` (see `madrasty-location-rules` skill). Never bypass it for "easier" testing or admin-only paths.

---

## What "right" looks like

```php
// app/Modules/Madrasty/Http/Requests/StoreListingRequest.php
public function rules(): array
{
    return [
        'listing_type' => ['required', Rule::in(['sale', 'exchange', 'donation', 'wanted'])],
        'price' => ['required_if:listing_type,sale', 'nullable', 'numeric', 'min:1'],
        'exchange_for_text' => ['required_if:listing_type,exchange', 'nullable', 'string', 'max:500'],
        // ... + photos required unless listing_type === 'wanted'
    ];
}

public function withValidator(Validator $v): void
{
    $v->after(function ($v) {
        $type = $this->input('listing_type');
        if ($type === 'donation' && $this->filled('price') && (float) $this->input('price') > 0) {
            $v->errors()->add('price', 'Donation listings must not set a price.');
        }
        if ($type === 'exchange' && $this->filled('price')) {
            $v->errors()->add('price', 'Exchange listings must not set a price.');
        }
        if ($type === 'wanted' && ($this->filled('price') || $this->filled('exchange_for_text'))) {
            $v->errors()->add('listing_type', 'Wanted listings must not set price or exchange_for_text.');
        }
    });
}
```

```php
// app/Modules/Madrasty/Services/ListingStatusService.php
private const ALLOWED = [
    'active' => ['reserved', 'sold', 'exchanged', 'donated', 'expired', 'rejected', 'deleted'],
    'reserved' => ['active', 'sold', 'exchanged', 'donated', 'expired'],
    // ... full map
];

public function transition(Listing $l, string $to): void
{
    abort_unless(in_array($to, self::ALLOWED[$l->status] ?? [], true), 422,
        "cannot transition listing from {$l->status} to {$to}");
    $l->update(['status' => $to]);
}
```

```php
// app/Modules/Madrasty/Http/Resources/ListingResource.php — donation price normalization
'price' => $this->listing_type === 'sale' ? $this->price : null,
'currency' => $this->price_currency,
```

## What "wrong" looks like

```php
// ❌ Generic mark-completed endpoint — corrupts type→closing-status mapping
Route::post('listings/{id}/mark-completed', [ListingController::class, 'markCompleted']);
```

```php
// ❌ Type-rule logic in the controller — bypasses 422 for non-sale types with price
public function store(Request $request) {
    if ($request->listing_type === 'donation') {
        $request->merge(['price' => null]); // silently drops bad input
    }
    Listing::create($request->all());
}
```

```php
// ❌ Free-form status setter — no transition map
$listing->update(['status' => $request->input('status')]);
```

```php
// ❌ Admin marking a listing sold (only the owner should)
public function adminMarkSold(Listing $listing) {
    $listing->update(['status' => 'sold']); // owner-only action
}
```

---

## Files this skill governs

- `app/Modules/Madrasty/Http/Controllers/V1/ListingController.php`
- `app/Modules/Madrasty/Http/Controllers/V1/AdminController.php` (only for `reject`/`hide`/`approve`)
- `app/Modules/Madrasty/Http/Requests/{Store,Update}ListingRequest.php`
- `app/Modules/Madrasty/Http/Resources/ListingResource.php`
- `app/Modules/Madrasty/Services/{ListingService,ListingStatusService,WantedMatchService}.php`
- `app/Modules/Madrasty/Models/Listing.php`
- `app/Modules/Madrasty/Policies/ListingPolicy.php`
- `app/Modules/Madrasty/Console/Commands/PruneExpiredReservations.php`
- `database/migrations/*_create_listings_table.php`
- `database/migrations/*_alter_listings_*.php`

If a request asks you to write code that breaks ANY of these rules, raise the conflict — don't comply silently.
