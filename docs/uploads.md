# Uploads & Media

## Routes / Controller
- Uploads handled by `App\Http\Controllers\UploadController@upload` (route name: `dashboard.media.upload`).
- Users module forms post files via `mediafiles[...]` fields and avatar input.

## Validation Rules
- Avatar: images only (`jpg,jpeg,png`), max 5 MB.
- National ID / Iqama: `jpg,jpeg,png,pdf`, max 20 MB.
- SCFHS, BLS, ACLS certificates: `jpg,jpeg,png,pdf`, max 10 MB.
- Other unspecified groups: default `jpg,jpeg,png,pdf,doc,docx,xls,xlsx,txt`, max 20 MB.
- Images rejected if dimensions exceed 6000x6000.
- Empty upload requests are rejected.

## Storage / Media
- Uses media library (Spatie) collections per field (`avatar`, `national_id_iqama`, `SCFHS`, `BLS`, `ACLS`, etc.).
- Errors are returned as JSON; client JS can surface validation failures.
