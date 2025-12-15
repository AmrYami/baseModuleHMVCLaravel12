# Users & Roles

## Users
- Routes under `/dashboard/users` with permissions:
  - List/view: `list-users` / `show-users`
  - Create/store: `create-users`
  - Edit/update: `edit-users`
  - Freeze/unfreeze: `block-users`
  - Approve/deactivate: `approve-profile`
- Profile editing: `/dashboard/users/{user}/edit` and `/dashboard/users/edit_profile/{user}`.
- Freeze/banning: freeze/unfreeze endpoints plus `banned_until` support.
- My profile: `/dashboard/my_profile` — view, edit profile, edit password.

## Roles
- CRUD for roles under `/dashboard/roles` with permissions `list-users-role`, `create-users-role`, `edit-users-role`, `delete-users-role`.
- Role permissions grouped by module; select-all per group in UI.

## Activity Logs
- `/dashboard/logs` (permission `users-logs`) shows activity log entries.

## Navigation
- Sidebar entries gated by the above permissions; routes defined in `app/Modules/users/routes/web.php`.
