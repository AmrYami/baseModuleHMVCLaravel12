# Settings

Settings screens live under `/dashboard/setting/{key}` (permission: `setting-notifications`).

## Notifications
- Route: `/dashboard/setting/notifications`
- Toggles for notification channels/behaviors (see form fields).

## Emails
- Route: `/dashboard/setting/emails`
- Per-action triggers (welcome, approved, rejected, exam_pass, exam_fail) with subject/template/body and enable toggle.
- Workflow step notifications: configure per template/step/event.
- “Send Test” uses current config; requires valid `MAIL_*`.

## Registration
- Route: `/dashboard/setting/registration`
- Controls default registration status and related options.
