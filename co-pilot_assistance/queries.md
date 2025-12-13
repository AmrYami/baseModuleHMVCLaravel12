Common Queries
==============

Fetch a user’s current profile submission
SELECT s.*
FROM submissions s
JOIN forms f ON f.id = s.form_id
WHERE s.user_id = :user_id AND f.key = 'profile'
ORDER BY s.updated_at DESC
LIMIT 1;

Get all fields for a form version with options
SELECT ff.*
FROM form_fields ff
WHERE ff.form_id = :form_id AND ff.deleted_at IS NULL
ORDER BY ff.position ASC;

Values for a submission, keyed by field
SELECT sv.form_field_id, sv.value
FROM submission_values sv
WHERE sv.submission_id = :submission_id;

Dataset options (static)
SELECT di.label, di.value, di.disabled
FROM hr_datasets d
JOIN hr_dataset_items di ON di.hr_dataset_id = d.id
WHERE d.key = :dataset_key AND d.is_active = 1
ORDER BY di.position ASC;

Change requests for a user
SELECT * FROM change_requests WHERE user_id = :user_id ORDER BY created_at DESC;

