Permissions Reference
=====================

Profile (end-user)
- hr.profile.view — view Complete Profile page.
- hr.profile.save — save or submit profile form.
- hr.uploads.create — upload files on profile form.
- hr.uploads.destroy — remove previously uploaded files.
- hr.change_requests.index — list user’s change requests.
- hr.change_requests.store — create a new change request.

Admin HR
- hospitals: hr.hospitals.index, hr.hospitals.create, hr.hospitals.store, hr.hospitals.edit, hr.hospitals.update, hr.hospitals.destroy
- Forms: hr.forms.index, hr.forms.create, hr.forms.store, hr.forms.edit, hr.forms.update, hr.forms.destroy, hr.forms.publish, hr.forms.duplicate
- Fields: hr.fields.index, hr.fields.create, hr.fields.store, hr.fields.edit, hr.fields.update, hr.fields.destroy
- Datasets: hr.datasets.index, hr.datasets.create, hr.datasets.edit, hr.datasets.destroy
- Dataset Items: hr.dataset_items.index, hr.dataset_items.create, hr.dataset_items.edit, hr.dataset_items.destroy
- Admin Change Requests: hr.change_requests.index, hr.change_requests.approve, hr.change_requests.reject
- Admin Submissions: hr.submissions.index, hr.submissions.approve, hr.submissions.reject
- Stages: hr.stages.store (create stage decision records)

Notes
- Route middleware attaches permissions per action; controllers no longer call middleware() directly.
- Ensure the role `User` has the Profile permissions to access `/profile/complete` after login.
- Dynamic hospital tabs (List Users) render only when the principal has `list-users` permission (Top MGT set).
