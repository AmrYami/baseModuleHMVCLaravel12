Database Schema Reference
=========================

Core Tables

hospitals
- id (PK)
- name, slug (unique per not-deleted), description, is_active, order
- timestamps
- soft deletes: deleted_at

forms
- id (PK)
- hospital_id (FK → hospitals.id)
- name, key, version
- published_at, retired_at, is_active
- timestamps
- Unique: (hospital_id, key, version)
- soft deletes: deleted_at

form_fields
- id (PK)
- form_id (FK → forms.id)
- key, label, type
- options (json), rules (json), ui (json), visibility (json)
- required (bool), policy (enum), position (int)
- timestamps
- soft deletes: deleted_at
- Indexes: (form_id, position)
- Unique: (form_id, key, deleted_at)

submissions
- id (PK)
- user_id (FK → users.id)
- form_id (FK → forms.id)
- status (string), submitted_at
- timestamps
- soft deletes: deleted_at

submission_values
- id (PK)
- submission_id (FK → submissions.id)
- form_field_id (FK → form_fields.id)
- value (json), edit_count (int), locked_at (timestamp)
- timestamps
- soft deletes: deleted_at
- Unique: (submission_id, form_field_id, deleted_at)

hr_datasets
- id (PK)
- key (unique per not-deleted), name, description, is_active
- meta (json) — `meta.source` can be `lookup:<key>`
- timestamps
- soft deletes: deleted_at

hr_dataset_items
- id (PK)
- hr_dataset_id (FK → hr_datasets.id)
- label, value, disabled (bool), position, extra (json)
- timestamps
- soft deletes: deleted_at
- Unique: (hr_dataset_id, value, deleted_at)

change_requests
- id (PK)
- submission_id (FK), form_field_id (FK), user_id (FK)
- current_value (json), requested_value (json), reason (text)
- status (pending|approved|rejected|applied), decided_by (FK), decided_at
- timestamps
- soft deletes: deleted_at

hr_stage_events
- id (PK)
- user_id (FK), admin_id (FK)
- stage_key (string), status (approved|rejected|pending), description (text, nullable)
- data (json, nullable)
- timestamps
- soft deletes: deleted_at

JSON Field Shapes
- form_fields.ui: { placeholder?, help_text?, rows?, multiple?, attrs?, source? }
- form_fields.options: [ { label, value, default?, disabled?, meta? } ]
- form_fields.rules: [ Laravel validation rules ]
- form_fields.visibility: [ { when:{ field, op, value }, show: bool } ]

Users JSON (translatable)
- users.name — Spatie Translatable with `en`, `ar` (e.g., `{ "en": "John Doe", "ar": "جون دو" }`)

Local Testing Support (Assessments)
-----------------------------------
- A minimal migration creates base Assessments tables locally if they don't exist to enable unit tests:
  - `assessment_topics`, `assessment_questions`, `assessment_question_topics`, `assessment_exams`, `assessment_exam_topics`.
- Additional phase migrations extend these tables for attempts/questions/options.
