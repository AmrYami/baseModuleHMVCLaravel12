---
name: madrasty-module-layout
description: Enforces that all Madrasty domain code lives under app/Modules/Madrasty/ with the agreed sub-structure and HMVC + repository pattern. Trigger when creating new files in app/, when extending base classes, or when planning where a new file should go.
---

# Madrasty Module Layout

The basemodule is HMVC: each domain lives in its own module under `app/Modules/<Name>/` and only the framework wiring (BaseModel, BaseRepository, abstractions, providers) lives in `app/`. Madrasty must follow the same layout — drift back to scattered `app/Models`, `app/Http/Controllers`, etc. is forbidden.

This skill loads when you create a new file or move things around. **Pick the right location before writing — relocating later costs PR churn.**

---

## Inviolable rules

### 1. Madrasty domain code lives ONLY under `app/Modules/Madrasty/`

The agreed sub-structure:

```text
app/Modules/Madrasty/
├── Http/
│   ├── Controllers/
│   │   └── V1/
│   │       ├── AuthController.php
│   │       ├── LocationController.php
│   │       ├── CategoryController.php
│   │       ├── GradeController.php
│   │       ├── EducationSystemController.php
│   │       ├── SchoolController.php
│   │       ├── ListingController.php
│   │       ├── FavoriteController.php
│   │       ├── ContactRequestController.php
│   │       ├── ReportController.php
│   │       ├── DeviceController.php
│   │       ├── MeController.php
│   │       └── AdminController.php
│   ├── Requests/
│   │   ├── StoreListingRequest.php  UpdateListingRequest.php  FilterListingsRequest.php
│   │   ├── StoreSchoolSuggestionRequest.php
│   │   ├── StoreContactRequestRequest.php  RateContactRequestRequest.php
│   │   ├── StoreReportRequest.php
│   │   ├── ... (one per write endpoint)
│   ├── Resources/
│   │   ├── ListingResource.php           OwnerListingResource.php
│   │   ├── SchoolResource.php            UserPublicResource.php
│   │   ├── LocationResource.php          CategoryResource.php
│   │   ├── ContactRequestResource.php    SavedSearchResource.php
│   │   └── ... (one per Eloquent model exposed)
│   └── Middleware/
│       └── EnsureNotMinor.php  (or similar Madrasty-specific middleware)
├── Models/
│   ├── Country.php       Governorate.php   City.php           Area.php
│   ├── EducationSystem.php  Grade.php      School.php         SchoolGrade.php
│   ├── Category.php
│   ├── Listing.php       ListingPhoto.php
│   ├── Favorite.php      ContactRequest.php  Report.php
│   ├── SavedSearch.php   UserBlock.php       SchoolMergeRequest.php
│   ├── DeviceToken.php   AccountDeletionRequest.php
│   └── ... (one per table)
├── Repositories/
│   ├── ListingRepositoryShow.php   ListingRepositoryStore.php
│   ├── SchoolRepositoryShow.php    SchoolRepositoryStore.php
│   ├── LocationRepositoryShow.php
│   └── ... (extend BaseRepositoryShow / BaseRepositoryStore)
├── Services/
│   ├── ListingService.php          ListingStatusService.php
│   ├── LocationValidationService.php  SearchListingsService.php
│   ├── SchoolService.php           ModerationService.php
│   ├── ContactRequestService.php   WantedMatchService.php
│   ├── SavedSearchDigestService.php  ReverseGeocodeService.php
│   ├── PhoneVerificationService.php  AccountDeletionService.php
│   └── ... (extend the base Service abstraction)
├── Policies/
│   ├── ListingPolicy.php           ContactRequestPolicy.php
│   ├── SchoolPolicy.php            ReportPolicy.php
│   └── AdminPolicy.php
├── Providers/
│   └── MadrastyServiceProvider.php  (registers routes, policies, observers, scheduled jobs)
├── Console/
│   └── Commands/
│       ├── PruneExpiredReservations.php
│       ├── DispatchSavedSearchDigest.php
│       └── PurgeAccountDeletionRequests.php
├── Notifications/
│   ├── ContactRequestAcceptedNotification.php
│   ├── SavedSearchMatchNotification.php
│   ├── ReservationExpiringNotification.php
│   └── ListingExpiringNotification.php
├── Observers/
│   ├── ListingObserver.php  (fires WantedMatch on create/update)
│   └── SchoolObserver.php   (computes name_normalized)
├── database/
│   ├── migrations/
│   ├── seeders/
│   │   ├── EgyptCountrySeeder.php
│   │   ├── EgyptGovernoratesSeeder.php
│   │   ├── CategoriesSeeder.php
│   │   ├── EducationSystemsSeeder.php
│   │   └── GradesSeeder.php
│   └── factories/
├── resources/
│   └── lang/
│       ├── ar/
│       │   ├── validation.php   listings.php   reports.php
│       └── en/
│           ├── validation.php   listings.php   reports.php
└── routes/
    └── api.php   # mounted at /api/v1 by MadrastyServiceProvider
```

### 2. Models extend `App\Models\BaseModel` (or `BaseAuthModel` for auth-able)

Every Madrasty model:

```php
namespace App\Modules\Madrasty\Models;
use App\Models\BaseModel;

class Listing extends BaseModel { /* ... */ }
```

This gives you `HasTranslations`, `LogsActivity`, `UsesHashids` for free. Do NOT extend `Illuminate\Database\Eloquent\Model` directly.

### 3. Repositories extend `BaseRepositoryShow` / `BaseRepositoryStore`

Read-side and write-side are SEPARATE classes (the basemodule's CQRS-lite split). Don't combine into a single `XxxRepository`.

```php
namespace App\Modules\Madrasty\Repositories;
use App\Repositories\BaseRepositoryShow;

class ListingRepositoryShow extends BaseRepositoryShow {
    public function model(): string { return Listing::class; }
    public function getFieldsSearchable(): array { return ['title', 'description', 'school_id']; }
}
```

### 4. Services extend `App\Abstratctions\Service` (note the basemodule's typo — keep it)

```php
namespace App\Modules\Madrasty\Services;
use App\Abstratctions\Service;
use App\Interfaces\ServiceStore;

class ListingService extends Service implements ServiceStore { /* ... */ }
```

### 5. Composer autoload

`composer.json` must register the namespace. The basemodule already has `Users\` mapped to `app/Modules/users`. Add `App\Modules\Madrasty\` mapped to `app/Modules/Madrasty/`. After adding, run `composer dump-autoload` (this is fine to commit — composer.lock change is small).

### 6. ServiceProvider registration

`MadrastyServiceProvider` registers:

- Route group `/api/v1` (loading `routes/api.php`).
- Policies (`Listing → ListingPolicy`, etc.).
- Observers (`Listing::observe(ListingObserver::class)`, etc.).
- Console scheduling (PruneExpiredReservations hourly, etc.).

The provider must be added to `bootstrap/providers.php` alongside `Users\Providers\UserServiceProvider`.

### 7. Madrasty-specific code is NEVER added to `app/Models/`, `app/Http/Controllers/`, `app/Services/`, etc.

The basemodule has files there — leave them alone (they're shared infrastructure or reference). Madrasty-domain files go into `app/Modules/Madrasty/...`. The single exception is editing the existing `User` model (`Users\Models\User` or `App\Models\User`) when adding Madrasty-specific user fields/methods — that's a model already used by the framework, can't be moved.

### 8. Routes file conventions

`app/Modules/Madrasty/routes/api.php` uses `Route::prefix(...)` ONLY to add Madrasty sub-prefixes (e.g. `madrasty/admin`). The top-level `/api/v1` prefix is added by the ServiceProvider — do not duplicate it inside the routes file.

### 9. Tests follow the layout

`tests/Feature/Madrasty/<DomainName>/<TestName>Test.php`. E.g. `tests/Feature/Madrasty/Listings/CreateListingTest.php`. Don't put tests at the top level of `tests/Feature/` — that's basemodule territory.

### 10. The four .claude/skills are the source of truth

If the rules in this file conflict with what feels intuitive when typing, follow the skill. The other three skills (`madrasty-location-rules`, `madrasty-listing-rules`, `madrasty-api-shape`) work alongside this one. If a planned change can't satisfy all four, raise the conflict instead of bypassing.

---

## What "right" looks like

```text
✅ app/Modules/Madrasty/Models/Listing.php
✅ app/Modules/Madrasty/Http/Controllers/V1/ListingController.php
✅ app/Modules/Madrasty/Http/Requests/StoreListingRequest.php
✅ app/Modules/Madrasty/Repositories/ListingRepositoryShow.php
✅ app/Modules/Madrasty/Services/ListingService.php
✅ app/Modules/Madrasty/database/migrations/2026_05_07_000001_create_listings_table.php
```

```php
// app/Modules/Madrasty/Providers/MadrastyServiceProvider.php
public function boot(): void
{
    $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    Route::prefix('api/v1')
        ->middleware('api')
        ->group(__DIR__.'/../routes/api.php');

    Listing::observe(ListingObserver::class);
    Gate::policy(Listing::class, ListingPolicy::class);
}
```

## What "wrong" looks like

```text
❌ app/Models/Listing.php                        // belongs in Modules/Madrasty/Models/
❌ app/Http/Controllers/Api/V1/ListingController.php   // belongs in Modules/Madrasty/Http/Controllers/V1/
❌ app/Services/Madrasty/ListingService.php      // wrong namespace; module path is Modules/Madrasty
❌ app/Modules/madrasty/Listing.php              // lowercase + flat = wrong on every dimension
```

```php
// ❌ Adding the global /api/v1 prefix INSIDE the routes file (already added by provider)
Route::prefix('api/v1')->group(function () {
    Route::get('/listings', [ListingController::class, 'index']);
});
// Result: /api/v1/api/v1/listings — never matches Flutter's calls.
```

```php
// ❌ Skipping BaseModel — loses HasTranslations + LogsActivity + UsesHashids
class Listing extends \Illuminate\Database\Eloquent\Model {
    /* ... */
}
```

```php
// ❌ Combined repo (no Show/Store split)
class ListingRepository extends BaseRepository { /* read AND write methods mixed */ }
```

---

## When this skill triggers

- Creating ANY new file under `app/`.
- Editing `composer.json` autoload section.
- Editing `bootstrap/providers.php`.
- Planning where a model / controller / service should live before writing it.
- Reviewing a CR comment that suggests adding code in a non-module location — push back and put it in the module.

If the user asks for a file in the wrong location, raise the conflict. If genuinely unsure where something belongs, ask — better than guessing wrong and refactoring.
