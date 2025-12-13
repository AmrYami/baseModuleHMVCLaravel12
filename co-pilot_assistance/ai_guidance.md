AI Guidance for Data Access
===========================

Primary Keys & Joins
- forms → form_fields (form_fields.form_id = forms.id)
- submissions → submission_values (submission_values.submission_id = submissions.id)
- submission_values → form_fields (submission_values.form_field_id = form_fields.id)
- datasets: hr_datasets → hr_dataset_items (hr_dataset_items.hr_dataset_id = hr_datasets.id)

Getting the Right Form Version
- Always use the latest published form for a hospital and key. In code: `Form::latestPublished($hospitalId, 'profile')->first()`.
- If querying directly, filter by `is_active = 1` and `published_at <= NOW()` and not retired.

Reading Field Values
- For scalar fields (text, number, email, date, radio, select single), the stored JSON is usually a simple scalar; extract with JSON operators when needed.
- For multi-choice (checkbox, select multiple), `value` is an array.
- For file fields, `value` is { "media_id": <int> }. Join to Media Library’s `media` table on `id = media_id` to get URLs/filenames.

Respect Visibility
- `FieldVisibility::visible(values, visibility)` controls whether a field should be shown. For analytics, you may ignore it, but for form completion checks, consider only visible fields.

Respect Policies
- `immutable` and `admin_unlock_once` restrict edits. Locked one‑time fields have `submission_values.locked_at` set.

Datasets vs Manual Options
- If `ui.source.type = 'dataset'`, resolve allowed values via dataset. If `lookup:` source, the options reflect current DB data and may change.

Performance Tips
- Key submission values by `form_field_id` for efficient lookups.
- Indexes exist on FK columns; use them in joins.

Filters (List Users)
- Basic filters: `name` (users.name JSON — en/ar), `user_name`, `email`, `mobile`, and `status` (latest stage `first_review`).
- Profile filters: pass `pf[<field_id>]` with a scalar or an array of values. A match occurs if JSON_EXTRACT(value,'$') = v OR JSON_CONTAINS(value, JSON_QUOTE(v)). Multiple values are OR’d within the same field; different fields are AND’d together.
