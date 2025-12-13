Project Overview
================

Stack
- Framework: Laravel (Jetstream + Fortify)
- AuthZ: Spatie Permissions
- Media: Spatie Media Library
- UI: Blade views (dashboard.mt layout) + vanilla JS helpers
- Domain: HR module (hospitals → Forms → Fields → Submissions) with Datasets, Change Requests, and Stage Events

Key Concepts
- Hospital: Groups forms per business area.
- Form: Versioned form definition keyed by `key` (e.g., `profile`) with `version`, `published_at`, and `is_active`.
- FormField: Individual input inside a form version. Types include `text`, `textarea`, `number`, `date`, `select`, `radio`, `checkbox`, `file`, `email`, `tel`. Has `required`, `policy` (`open|immutable|admin_unlock_once`), `position`, `rules`, and `ui` (placeholder/help/multiple/attrs/source).
- Dataset: Re‑usable option lists for options inputs (select/radio/checkbox). Can be static (`hr_dataset_items`) or backed by dynamic DB lookups via `meta.source = lookup:<key>`.
- Submission: A user’s values for a specific form version (`status = draft|submitted|approved|rejected`).
- SubmissionValue: Stores atomic value per `(submission, form_field)` with `edit_count` and `locked_at` for one‑time edits.
- Change Request: User request to edit locked fields.
- Stage Event: Admin decision records for HR stages (e.g., `first_review`), with `status` approved|rejected and optional description; used for dashboards, filters, and audit trail.

Where Things Live
- Routes: `routes/web.php`
- Admin HR controllers: `app/HR/Http/Controllers/Admin/*`
- Profile controllers: `app/HR/Http/Controllers/Profile/*`
- Requests/Validation: `app/HR/Http/Requests/*`
- Models: `app/HR/Models/*`
- Support utilities: `app/HR/Support/*`
- Views:
  - Admin form edit: `resources/views/admin/hr/forms/edit.blade.php`
  - Field edit: `resources/views/admin/hr/fields/edit.blade.php`
  - Profile complete: `resources/views/profile/complete.blade.php`
  - Dynamic field component: `resources/views/components/hr/dynamic-field.blade.php`
  - Hospital user list: `resources/views/hospitals/users/index.blade.php`
  - Hospital user read‑only profile: `resources/views/hospitals/users/show.blade.php`

Login Redirects
- Post‑login redirect (Fortify LoginResponse):
  - Users with role `User` → `route('profile.complete')`
  - Others → `route('dashboard')`

Recent Additions
- Stage decisions (Approve/Reject) recorded via `StageEvent`, available from the hospital list and the user profile view.
- Dynamic hospital tabs in sidebar (guarded by `list-users` permission) with List Users page and full read‑only profile.
- Powerful filters on List Users: top basic filters (name/username/email/mobile/status) and per‑field profile filters with automatic select/text inputs.
- Locked field UX: one‑time editable fields render disabled when locked and are ignored by validation/persistence.
- Translated user name capture on `profile/complete` (Spatie Translatable on `users.name` JSON `{ en, ar }`).
- Broad soft‑delete support across HR models/tables to preserve history.

Role‑Driven Hospital (Users)
---------------------------
- Pages: `dashboard/users/create`, `dashboard/users/{id}/edit`.
- Behavior:
  - If Role = `User` → show Hospital and make it required.
  - If Role ≠ `User` → hide Hospital and do not require it.
  - Switching roles toggles instantly via JS; name/email/username/mobile never change.
- Server validation: Hospital is required only when the selected role is the role named `User`.
- Edit: If the current role is `User`, Hospital is visible and prefilled; otherwise it’s hidden.
- Accessibility: validation messages render inline and the toggle does not disrupt focus.
