# Architecture & Activity Log

## Layers
- **Controllers**: HTTP-only concerns (authz, validation via Form Requests, picking the right view/JSON). They delegate business work.
- **Services**: Orchestrate business rules and transactions (e.g., creating/updating users, freezing accounts, sending mail). They call repositories and side-effects (mail/queue/logging).
- **Repositories**: Data access for a model (queries/CRUD) so services aren’t tied to Eloquent directly.
- **Flow**: Request → Controller (auth/validation) → Service (business logic) → Repository (persistence) → Response (view/JSON).

## Base Models
- Models share common behavior via a base class/traits (dates, casts, media, logs). Where `Spatie\Activitylog\Traits\LogsActivity` is used, create/update/delete events are logged automatically.

## Activity Log (Spatie)
- Package: `spatie/laravel-activitylog` logs to the `activity_log` table.
- **Auto-logging**: Models with `LogsActivity` trait log created/updated/deleted events with dirty attributes.
- **Manual logging**:
  ```php
  activity()
      ->causedBy(auth()->user())      // optional, auto-set when authenticated
      ->performedOn($model)           // optional: relates to a model
      ->withProperties(['key' => 'value']) // optional metadata
      ->log('event-name');
  ```
- **Viewing**: `/dashboard/logs` (permission `users-logs`) shows entries.
- **Attribution**: In authenticated requests, the current user is attached unless overridden with `causedBy()`.

## Where to add logic
- **Controller**: Only for HTTP concerns and permission checks.
- **Service**: When business rules span multiple repositories or side-effects (mail, events, logging).
- **Repository**: For reusable queries or persistence operations.
