Reporting and Analysis Examples
===============================

SQL Snippets

1) Submissions by status for Profile form
SELECT s.status, COUNT(*)
FROM submissions s
JOIN forms f ON f.id = s.form_id
WHERE f.key = 'profile'
GROUP BY s.status;

2) Latest submitted profiles per hospital
SELECT f.hospital_id, COUNT(*) AS submitted
FROM submissions s
JOIN forms f ON f.id = s.form_id
WHERE f.key = 'profile' AND s.status = 'submitted'
GROUP BY f.hospital_id;

3) Users missing required fields (open/admin_unlock_once only)
-- Example for current published Profile version per hospital
SELECT s.user_id, ff.key
FROM submissions s
JOIN forms f ON f.id = s.form_id
JOIN form_fields ff ON ff.form_id = f.id AND ff.required = 1
LEFT JOIN submission_values sv
  ON sv.submission_id = s.id AND sv.form_field_id = ff.id
WHERE f.key = 'profile' AND (sv.id IS NULL OR sv.value IS NULL);

4) Distribution of a select field (e.g., country)
SELECT JSON_UNQUOTE(JSON_EXTRACT(sv.value, '$')) AS value, COUNT(*)
FROM submissions s
JOIN forms f ON f.id = s.form_id
JOIN form_fields ff ON ff.form_id = f.id AND ff.key = 'country'
JOIN submission_values sv ON sv.submission_id = s.id AND sv.form_field_id = ff.id
WHERE f.key = 'profile'
GROUP BY value
ORDER BY COUNT(*) DESC;

5) Change requests status breakdown
SELECT status, COUNT(*)
FROM change_requests
GROUP BY status;

6) Latest stage decisions (first_review)
SELECT COALESCE(se.status,'pending') AS status, COUNT(*)
FROM users u
LEFT JOIN (
  SELECT se1.user_id, se1.status
  FROM hr_stage_events se1
  WHERE se1.stage_key = 'first_review'
  AND se1.id IN (
    SELECT se2.id FROM hr_stage_events se2
    WHERE se2.user_id = se1.user_id AND se2.stage_key = se1.stage_key
    ORDER BY se2.created_at DESC, se2.id DESC LIMIT 1
  )
) se ON se.user_id = u.id
GROUP BY COALESCE(se.status,'pending');

Eloquent Patterns
- Get latest published Profile form for a user’s hospital:
  $form = Form::latestPublished($user->hospital_id, 'profile')->first();

- Values keyed by field id:
  $values = $submission->values->keyBy('form_field_id');

- Filter visible fields for evaluation:
  foreach ($form->fields as $f) {
    if (!FieldVisibility::visible($input, $f->visibility)) continue;
  }
