Field Policies and Locking
==========================

Policies
- open: User can edit any time.
- immutable: Once a value exists, further edits should be prevented.
- admin_unlock_once: User can set or edit once, then the value locks. Admin can unlock for a single subsequent edit via Change Request.

Enforcement Points
- UI: Dynamic field component disables inputs for locked `admin_unlock_once` values and hides the file Remove button.
- Validation: `StoreSubmissionRequest` removes rules for locked fields to avoid required errors.
- Persistence: `StoreSubmissionRequest` skips updating locked fields; `Submission::upsertValue()` throws if you attempt updates on immutable or locked one‑time fields unless forced during admin workflows.

Locking Mechanics
- On save/update, `Submission::upsertValue()` increments `edit_count`. For `admin_unlock_once`, it sets `locked_at` when value exists.
- `SubmissionValue::isLocked()` returns true when `locked_at` is not null.
- Admin approval path should call `unlockOnce()` before applying the new value, then re‑lock.

