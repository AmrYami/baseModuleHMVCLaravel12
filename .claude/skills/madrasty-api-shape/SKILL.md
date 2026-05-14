---
name: madrasty-api-shape
description: Locks the /api/v1 prefix, JSON envelope, pagination shape, error shape, and forbids exposing PII (phone, email, full address, mobile) in any public API response. Trigger when editing API resources, controllers, FormRequests, route files, or any file under app/Modules/Madrasty/Http.
---

# Madrasty API Shape

The Flutter app and the Laravel API are bound by a contract: every endpoint under `/api/v1/*` returns a consistent JSON envelope, paginates the same way, fails the same way, and **never** leaks PII (phone, email, full address) of one user to another.

This skill loads whenever you touch the API surface. **Each rule below is a contract — Flutter parses against it and breaks if you change the shape unilaterally.**

---

## Inviolable rules

### 1. Prefix and routing

- All Madrasty endpoints live under `/api/v1/*`. No `v0`, no unversioned.
- Routes are declared in `app/Modules/Madrasty/routes/api.php` and registered by `MadrastyServiceProvider` with the `api` middleware group + `/api/v1` prefix.
- Auth-required routes use `auth:api` (Passport) — NOT `auth:sanctum` and NOT `jwt.auth`.

### 2. Response envelope

Single-resource:

```json
{ "data": { ... } }
```

Collection (paginated):

```json
{
  "data": [ ... ],
  "meta": {
    "current_page": 1,
    "per_page": 20,
    "total": 312,
    "last_page": 16
  },
  "links": {
    "first": "...?page=1",
    "last":  "...?page=16",
    "prev":  null,
    "next":  "...?page=2"
  }
}
```

Implementation: extend `JsonResource::collection()` for paginators (Laravel's default already produces this shape — DON'T wrap it in another array). Don't invent `{ "items": [...], "count": N }` or `{ "result": ..., "ok": true }` shapes.

### 3. Error envelope

Validation (422):

```json
{ "message": "The given data was invalid.", "errors": { "field": ["msg"] } }
```

Auth / not-found / forbidden (401/403/404/422/500):

```json
{ "message": "Human-readable error", "code": "snake_case_machine_code" }
```

Use Laravel's default error rendering for 422; for 401/403/404 use a small `ApiExceptionRenderer` that returns the shape above. Never return HTML, never return a plain string body.

### 4. PII forbidden in public responses

The following fields are NEVER serialized in a public response (any endpoint reachable by a user other than the resource's owner):

- `users.phone`, `users.mobile`
- `users.email`
- Full street address (the location chain Country/Governorate/City/Area is fine — it's coarse)
- `users.password`, `remember_token`, `two_factor_secret`, `two_factor_recovery_codes`

Public-safe user fields are:

- `id` (or `hashid` if exposed externally)
- `name` (display name only — first name + initial recommended)
- `is_phone_verified` (boolean — the badge from §65.9)
- `created_at` ("member since")
- aggregate counters: `completed_listings_count`, `completion_rating`

The `/me` endpoint may return the user's OWN email/mobile (it's their own data). No other endpoint may.

### 5. Owner-vs-public resources

When a resource has both an owner view and a public view, write **two** Resource classes — `ListingResource` (public) and `OwnerListingResource` (extends or composes the first; adds `views_count`, `favorites_count`, `contact_requests_count`, `reserved_until`, etc.). Use the right one in each controller method. NEVER conditionally branch within a single Resource (`when($this->id === auth()->id(), ...)` everywhere is hard to audit).

### 6. Hashids in public IDs (if used)

If a model uses `UsesHashids`, the public-facing `id` field in resources should be the hashed ID. Internal admin endpoints may expose raw integers. Internal references (foreign keys in the same response) should match — don't mix hashed and raw in the same payload.

### 7. Pagination defaults

- Default `per_page = 20`.
- Allowed range `1 ≤ per_page ≤ 50`. Reject 0 or 51+.
- Always paginate listing/search endpoints — never return a full collection.
- For very-large-N endpoints (admin: pending listings), use cursor pagination via `cursorPaginate()` so deep pages don't shift on insert.

### 8. Filter rules belong in FormRequest

`FilterListingsRequest` validates `country_id` (required), `governorate_id` (required), `city_id`, `area_id`, `school_id`, `grade_id`, `category_id`, `listing_type`, `condition`, `min_price`, `max_price`, `q`, `sort`. If the controller is reading `$request->input(...)` directly without a FormRequest, that's wrong — the contract isn't enforced.

### 9. WhatsApp share URLs and deep links

When generating a share URL for a listing, build it server-side from `config('app.url')` + `/listing/{id}` (or hashid). The Flutter side wraps that in a `wa.me/?text=...` URL. Don't let either side hard-code the domain.

### 10. Locale negotiation

Translatable fields (e.g., school `name` is JSON `{ar, en}`) are returned in the requested locale based on the `Accept-Language` header (default `ar`). Resources use `getTranslation('name', app()->getLocale())` — NOT the raw JSON.

---

## What "right" looks like

```php
// app/Modules/Madrasty/Http/Resources/UserPublicResource.php
class UserPublicResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->hashid,
            'name' => $this->name,
            'is_phone_verified' => (bool) $this->is_phone_verified,
            'completion_rating' => [
                'positive' => $this->completion_positive_count,
                'negative' => $this->completion_negative_count,
            ],
            'member_since' => $this->created_at?->toIso8601String(),
            // NO email, NO mobile, NO phone, NO national_id.
        ];
    }
}
```

```php
// routes registration in app/Modules/Madrasty/Providers/MadrastyServiceProvider.php
public function boot(): void
{
    Route::prefix('api/v1')
        ->middleware('api')
        ->group(function () {
            require __DIR__.'/../routes/api.php';
        });
}
```

```php
// FilterListingsRequest validates the contract
public function rules(): array
{
    return [
        'country_id' => 'required|integer|exists:countries,id',
        'governorate_id' => 'required|integer|exists:governorates,id',
        'city_id' => 'sometimes|integer|exists:cities,id',
        'per_page' => 'sometimes|integer|min:1|max:50',
        // ...
    ];
}
```

## What "wrong" looks like

```php
// ❌ PII leak: includes mobile in a public listing response
class ListingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'owner_mobile' => $this->user->mobile, // NO
        ];
    }
}
```

```php
// ❌ Wrong envelope — Flutter parser breaks
return response()->json([
    'ok' => true,
    'result' => $listings,
    'count' => $listings->count(),
]);
```

```php
// ❌ Reading filters without a FormRequest — contract not enforced
public function index(Request $request)
{
    $listings = Listing::where('country_id', $request->input('country_id', 1))->paginate();
    return $listings;
}
```

```php
// ❌ Using auth:sanctum on a Madrasty endpoint
Route::middleware('auth:sanctum')->get('/api/v1/me', [MeController::class, 'show']);
// Madrasty uses Passport — auth:api.
```

```php
// ❌ Returning a raw string for an error
return response('not found', 404);
```

---

## Files this skill governs

- `app/Modules/Madrasty/Http/Resources/*.php`
- `app/Modules/Madrasty/Http/Controllers/V1/*.php`
- `app/Modules/Madrasty/Http/Requests/*.php`
- `app/Modules/Madrasty/Providers/MadrastyServiceProvider.php`
- `app/Modules/Madrasty/routes/api.php`
- `app/Modules/Madrasty/Exceptions/ApiExceptionRenderer.php` (or wherever the API renderer is wired)

When making changes here, run through every rule above. Flutter integration tests will fail if the envelope/error shape drifts — the `feedback_testing_no_mocks.md` rule means those tests catch contract drift in real network calls.
