Testing & Quality
=================

Test Runner
-----------
- Framework: Pest (PHPUnit)
- Suites:
  - Unit (primary coverage): `vendor/bin/pest --testsuite=Unit`
  - Feature (scoped to project-specific tests): `vendor/bin/pest --testsuite=Feature`
- All: `vendor/bin/pest`

Test Database
-------------
- Tests use MySQL database `hr_test` by default (see `phpunit.xml`).
- Create it once locally: `CREATE DATABASE hr_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`
- Ensure `DB_HOST`, `DB_PORT`, `DB_USERNAME`, `DB_PASSWORD` are set in `.env`.

Coverage Highlights
-------------------
- HR Admin: hospitals, Forms (publish/duplicate), Fields (bulk presets), Datasets + Items, Stage Events, Submissions index.
- HR Profile: Complete Profile, Change Requests (Profile + Admin), Uploads (API + Web).
- HR Support: RuleBuilder, FieldVisibility, Datasets/LookupOptions (cache).
- Settings: Helpers (getSettingJson, sendActionMail with validator), Emails settings + Send Test email.
- Assessments: Admin (Topics, Questions, Exams), Candidate (Attempt lifecycle, Preview), Services (Exam assembly), Gate middleware behaviors.

Known Skip
----------
- One Exam Gate redirect assertion is skipped due to named route binding nuances in the test kernel; other gate behaviors pass.

Local Facilitation
------------------
- A minimal migration creates base Assessments tables if missing to enable tests (`2025_10_14_070000_create_assessments_base_tables.php`).
- External HTTP is faked in tests (ZeroBounce validator calls), so no network is needed.
- Media uploads use `Storage::fake('public')` and set `media-library.disk_name` to `public` during tests.

Role‑Driven Hospital (Users) Tests
---------------------------------
- Registration unit tests confirm Hospital is required only when Role = `User`.
- UI toggle is tested indirectly by server rules; client-side JS mirrors the same logic.

CI Suggestion
-------------
- Run Unit first, then Feature for faster feedback:
  - `vendor/bin/pest --testsuite=Unit && vendor/bin/pest --testsuite=Feature`

