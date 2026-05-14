---
name: madrasty-location-rules
description: Enforces the Country → Governorate → City → Area chain integrity for any Madrasty code that reads or writes a location reference. Trigger when editing files under app/Modules/Madrasty/{Models,Services,Http/Requests,Http/Controllers}, listing/school/user-location migrations, or LocationValidationService.
---

# Madrasty Location Rules

Madrasty's entire search/recommendation surface is built on a **strict geographic hierarchy**:

```text
Country → Governorate → City → Area
```

If this chain is corrupt — e.g. an Area row whose `governorate_id` doesn't match its `city`'s `governorate_id` — every downstream filter, recommendation, and analytics number gets quietly wrong. The whole product reputation rests on these IDs being internally consistent. **Treat it as inviolable.**

This skill loads whenever you're touching code that reads or writes a `country_id`, `governorate_id`, `city_id`, or `area_id`. **Apply every rule below before sending any patch.**

---

## Inviolable rules

### 1. Validate the entire chain server-side, on every write

Any FormRequest / Service that accepts `area_id` or `city_id` MUST verify, in code:

- `governorate.country_id == request.country_id`
- `city.governorate_id == request.governorate_id`
- `city.country_id == request.country_id`
- `area.city_id == request.city_id`
- `area.governorate_id == request.governorate_id`
- `area.country_id == request.country_id`

`'exists:areas,id'` is **not enough** — it only checks the area exists, not that it belongs to the rest of the chain the user submitted. Always use `LocationValidationService` (or write rules through it) — never duplicate the chain checks inline.

### 2. Never trust client-supplied parent IDs in isolation

The Flutter app may submit `country_id`, `governorate_id`, `city_id`, `area_id` together. **Re-derive the chain from `area_id` server-side** and reject the request if the client-supplied parents don't match the derived ones. This blocks IDs being mixed across regions (intentional or accidental).

### 3. Schools are also part of the location graph

When `school_id` is present on a write, validate:

- `school.country_id == request.country_id`
- `school.governorate_id == request.governorate_id`
- (Optional, per category rule) `school.city_id == request.city_id`

### 4. Filter queries must apply the chain in correct order

For listings/schools listing endpoints:

1. `country_id` is REQUIRED — never query Madrasty data without it.
2. `governorate_id` is REQUIRED for any user-facing list.
3. `city_id`, `area_id` further narrow.
4. When user filters by `school_id`, the `country_id` + `governorate_id` filters MUST still apply (the school's own location must satisfy them).

### 5. Index discipline

Every query that filters by location must hit a covering index. The required composite indexes (per analysis §34.2) are:

- `(status, country_id, governorate_id)`
- `(status, country_id, governorate_id, city_id, area_id)`
- `(status, school_id, grade_id)`

If you add a query that doesn't fit one of these, either add the index in the same migration or push back on the query.

### 6. Reverse-geocode is read-only and never bypasses validation

`GET /api/v1/locations/reverse-geocode` returns the *suggested* nearest area/city/governorate/country, but the suggestion is then validated by the same FormRequest as a manual selection. Never short-circuit `LocationValidationService` for reverse-geocoded values.

---

## What "right" looks like

```php
// app/Modules/Madrasty/Services/LocationValidationService.php
public function ensureChainValid(array $payload): void
{
    $area = Area::with('city.governorate')->findOrFail($payload['area_id']);

    abort_if($area->city_id !== (int) $payload['city_id'], 422,
        'area does not belong to city');
    abort_if($area->city->governorate_id !== (int) $payload['governorate_id'], 422,
        'city does not belong to governorate');
    abort_if($area->city->governorate->country_id !== (int) $payload['country_id'], 422,
        'governorate does not belong to country');
}
```

```php
// app/Modules/Madrasty/Http/Requests/StoreListingRequest.php
public function withValidator(Validator $validator): void
{
    $validator->after(function ($v) {
        try {
            app(LocationValidationService::class)->ensureChainValid($this->validated());
        } catch (HttpException $e) {
            $v->errors()->add('location', $e->getMessage());
        }
    });
}
```

## What "wrong" looks like

```php
// ❌ Trusts client-supplied IDs without cross-validation
public function rules(): array
{
    return [
        'country_id' => 'required|exists:countries,id',
        'governorate_id' => 'required|exists:governorates,id',
        'city_id' => 'required|exists:cities,id',
        'area_id' => 'required|exists:areas,id',
    ];
}
// Now a request with country_id=Egypt + area_id=an_area_in_KSA passes validation.
```

```php
// ❌ Filters the listings table by area only — skips the rest of the hierarchy
Listing::where('area_id', $request->area_id)->where('status', 'active')->get();
// Query plan won't use the (status, country_id, governorate_id) composite index.
```

```php
// ❌ Inline duplicate validation (drifts from LocationValidationService over time)
if ($city->governorate_id !== $request->governorate_id) abort(422);
// Use LocationValidationService instead — single source of truth.
```

---

## Files this skill governs

- `app/Modules/Madrasty/Http/Requests/*Listing*.php`
- `app/Modules/Madrasty/Http/Requests/*School*.php`
- `app/Modules/Madrasty/Http/Requests/Update*ProfileRequest.php` (user location)
- `app/Modules/Madrasty/Services/LocationValidationService.php`
- `app/Modules/Madrasty/Services/SearchListingsService.php`
- `app/Modules/Madrasty/Models/{Country,Governorate,City,Area,School}.php`
- `database/migrations/*_create_{governorates,cities,areas,schools,listings}_table.php`

When editing these, run through every rule above. If a rule conflicts with what the user is asking, raise the conflict instead of bypassing the rule.
