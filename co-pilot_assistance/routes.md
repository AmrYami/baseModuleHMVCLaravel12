HTTP Routes (Key Endpoints)
===========================

Auth / Redirects
- POST /login → Fortify; post-login redirect via LoginResponse:
  - role `User` → `route('profile.complete)`
  - others → `route('dashboard')`

Profile
- GET  profile/complete → CompleteProfileController@show (permission: hr.profile.view) — name: profile.complete
- POST profile/complete → CompleteProfileController@store (permission: hr.profile.save) — name: profile.complete.store
- POST profile/upload → UploadController@store (permission: hr.uploads.create) — name: profile.upload
- DELETE profile/upload/{media} → UploadController@destroy (permission: hr.uploads.destroy) — name: profile.upload.destroy
- GET  profile/change-requests → ChangeRequestController@index (permission: hr.change_requests.index) — name: profile.change_requests.index
- POST profile/change-requests → ChangeRequestController@store (permission: hr.change_requests.store) — name: profile.change_requests.store

hospitals
- GET  hospitals/{hospital}/users → CategoryUsersController@index (permission: list-users) — name: hospitals.users.index
- GET  hospitals/{hospital}/users/{user} → CategoryUsersController@show (permission: list-users) — name: hospitals.users.show

Admin HR
- Resource: admin/hr/hospitals → HrCategoryController (permissions per action)
- Resource: admin/hr/hospitals/{hospital}/forms → HrFormController (shallow; permissions per action)
- Resource: admin/hr/forms/{form}/fields → HrFormFieldController (shallow; permissions per action)
- POST admin/hr/forms/{form}/publish → HrFormController@publish — name: admin.hr.forms.publish
- POST admin/hr/forms/{form}/duplicate → HrFormController@duplicate — name: admin.hr.forms.duplicate
- POST admin/hr/forms/{form}/fields/bulk → FormFieldController@bulkStore — name: admin.hr.forms.fields.bulk
- POST admin/hr/stage-events → StageEventController@store — name: admin.hr.stages.store
