# exchange_backend — Madrasty Used App API

Backend for **مدرستي المستعملة** ("Madrasty Used App"): a mobile-only marketplace for school-related used items (sell / exchange / donate), filtered by a strict location hierarchy (Country → Governorate → City → Area) plus School and Grade.

- **Spec:** [`madrasty-used-app-detailed-analysis.md`](../madrasty-used-app-detailed-analysis.md) (lives outside the repo, in the project root).
- **Mobile client:** [exchange_flutter](https://github.com/AmrYami/exchange_flutter) (separate repo).
- **API base URL (local):** `http://127.0.0.1:8000/api/v1`
- **Auth:** Laravel Passport (OAuth2 Password Grant). Login accepts **email or mobile** as `username`.
- **Architecture:** HMVC modules + Repository/Service pattern (see "Module pattern" below).

## Stack

| Layer | Tech |
|---|---|
| Framework | Laravel 13 (PHP ^8.5) |
| Auth | Laravel Passport (OAuth2) |
| Permissions | spatie/laravel-permission (roles: `user`, `moderator`, `admin`) |
| Translations | spatie/laravel-translatable |
| Media | spatie/laravel-medialibrary (listing photos: jpg/jpeg/png/webp, ≤4 MB) |
| Audit | spatie/laravel-activitylog |
| Tests | Pest + PHPUnit |
| Style | Laravel Pint |

## Module pattern

All Madrasty domain code lives under `app/Modules/Madrasty/` (mirrors the existing `app/Modules/users/` reference module):

```
app/Modules/Madrasty/
  Http/{Controllers/V1, Requests, Resources}/
  Models/                                 # extend App\Models\BaseModel
  Repositories/                           # extend BaseRepositoryShow / BaseRepositoryStore
  Services/                               # extend the base Service abstraction
  Policies/
  Providers/MadrastyServiceProvider.php
  database/{migrations, seeders, factories}/
  resources/lang/{ar, en}/
  routes/api.php                          # mounted at /api/v1
```

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan passport:install
php artisan serve  # http://127.0.0.1:8000
```

For the test suite (real HTTP, no mocks — see "Testing" below):

```bash
php artisan migrate:fresh --seed --env=testing
vendor/bin/pest
```

## Testing rules

- **Every feature must ship with a test.** Pest unit and feature tests under `tests/Unit/Madrasty/` and `tests/Feature/Madrasty/`.
- **Flutter integration tests hit the real local backend** — no HTTP mocks. Start `php artisan serve` first; then run `flutter test integration_test/`.

## Linked docs

- [`docs/architecture.md`](docs/architecture.md), [`docs/auth.md`](docs/auth.md), [`docs/security.md`](docs/security.md), [`docs/uploads.md`](docs/uploads.md), [`docs/users.md`](docs/users.md), [`docs/settings.md`](docs/settings.md)
- [`reports/madrasty-implementation-progress.md`](reports/madrasty-implementation-progress.md) — milestone log
- [`reports/madrasty-api-endpoints.md`](reports/madrasty-api-endpoints.md) — endpoint inventory
- [`reports/madrasty-open-issues.md`](reports/madrasty-open-issues.md) — known issues
- [`.claude/skills/`](.claude/skills/) — project-local AI skills enforcing module layout, listing rules, location hierarchy, and API/PII shape

---
