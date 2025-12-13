implement Outlook + Gmail email evidence attached to any step action (approve / reject / return). It’s phased, secure, and fits your workflow engine.

Email Evidence Integration (Outlook + Gmail)
1) What we want (user stories)

As a department user (Operations/Logistics/HR/Supervisor), when I approve/reject/return a step, I can attach one or more emails (with or without attachments) as evidence for that decision.

As HR/Admin, I can later open/view the attached email(s), see who attached them, and audit the decision.

As a security/compliance owner, I can control scopes, store minimal data, and apply retention rules.

2) Providers & scopes (principles)

Outlook (Microsoft 365 / Exchange Online) → Microsoft Graph: Mail.Read (min to read metadata/body); offline_access for refresh tokens; (optional) Calendars.Read not needed here.

Gmail → Gmail API: gmail.readonly (metadata + body + attachments); offline access for refresh tokens.

Least privilege: start with read‑only for messages; no send/modify.

3) Phased delivery
Phase 0 — Quick win (no OAuth)

Manual link: On step action, provide an “Attach evidence → Add Email Link” with fields:

Provider (gmail|outlook|other), Subject, From, Sent at, and URL (copy‑paste from the web client).

Pros: zero integration; Cons: link may break (permissions, moved mail).

Forward‑to‑ingest: Generate a unique ingest address per step instance (e.g., wf+{workflowId}-{stepId}@yourdomain).

The user forwards the relevant email to that address.

Backend parses EML (subject/from/to/date/body/attachments) and attaches a snapshot to the step action.

Pros: robust snapshot; Cons: requires mail routing & parsing.

This phase gives you evidence today while Phase 1 is built.

Phase 1 — User OAuth + In‑App Email Picker (recommended MVP)

Connect mailbox (per user, once):

In My Settings → Email Accounts, user clicks Connect Gmail or Connect Outlook (OAuth).

Store provider, account email, and encrypted refresh token.

Attach email at step action time:

On the Approve/Reject/Return modal, add “Attach Email”:

Search box (subject / from / date range) + quick filters (e.g., “From candidate’s email” / “Last 30 days”).

List results (Subject, From, Date, small snippet).

User ticks one or more emails → Attach.

Storage choice (configurable):

Link‑only: store provider + messageId + immutable metadata (subject/from/to/date/size) + “Open in Gmail/Outlook” link. Light footprint, relies on mailbox access.

Snapshot: fetch and store a read‑only copy (sanitized HTML/text) and, optionally, attachments. Ensures availability even if the original is deleted; heavier storage.

The step action’s timeline card shows “Attached emails (N)” with Subject/From/Date and Open / View Snapshot.

Phase 2 — Auto‑suggest & Watchers (nice to have)

Auto‑suggest: When opening the action modal, the system suggests emails that match the candidate’s address and time window, or contain the workflow/step reference in subject.

Webhooks:

Gmail: Pub/Sub watch on threads labeled “Assessments” (optional).

Outlook: Graph subscriptions on messages for connected users, scoped to a folder (optional).

Purpose: pre‑index metadata only; still attach on demand (explicit).

Phase 3 — Native Add‑ins (premium UX)

Outlook Add‑in / Gmail Add‑on:

A side‑panel button inside the email client: “Attach to Workflow”.

User selects Workflow → Step → Action and hits Attach.

System posts the messageId + metadata (and snapshot if configured) to the step action.

4) Data model (tables/fields)

step_actions (already in your workflow design):
id, step_instance_id, action (approve/reject/return), actor_user_id, created_at, comment

step_action_evidences (new):

id, step_action_id, type = 'email' | 'file' | 'url' | 'note'

For email:

provider (gmail|outlook|other), provider_account_email

message_id, thread_id (provider IDs)

subject, from_name, from_email, to_json, cc_json, bcc_json

sent_at, size_bytes

mode (link_only | snapshot)

snapshot_path (if snapshot), attachments_count, attachments_total_bytes

body_hash_sha256, attachment_hashes_json (for chain‑of‑custody)

created_by, created_at

Index by (step_action_id), (provider, message_id).

email_accounts (connected mailboxes):

id, user_id, provider, account_email, scopes_json, token_encrypted, refresh_token_encrypted, expires_at, status

All secrets encrypted at rest; rotate tokens on schedule.

5) Step action flow (approve/reject/return)

User opens the action modal → clicks Attach Email.

If not connected, prompt Connect Gmail/Outlook (OAuth).

Modal shows Search + Results (with quick filters).

User selects email(s) → choose Link‑only or Snapshot per item (default per system policy).

Submit action (with optional comment).

System creates step_action + step_action_evidences records; if snapshot mode → background job fetches body/attachments and stores them; update evidence record once done.

Timeline shows the action + Attached emails. Opening a link uses the provider URL; snapshot opens internal viewer.

6) Security & compliance

Scopes: Mail.Read (Graph) / gmail.readonly (Gmail), offline_access. No send/modify.

Encryption: tokens and snapshots encrypted at rest; limit retention via policy (e.g., purge snapshots after N days while keeping metadata).

Access control: Only users with step permissions can attach/view evidence for that step.

PII: mask sensitive content in list views (show subject/from/date only); preview body only on explicit click.

Anti‑tamper: store hashes (SHA‑256) for body and files; log who attached what and when.

Legal hold: ability to mark a workflow instance (or evidence) as non‑deletable during investigations.

S/MIME / encrypted emails: if body is encrypted, store metadata + the fact it’s encrypted; snapshot may be unavailable.

7) UI/UX (concise)

Action modal: “Attach evidence” → “Attach Email” (button).

Picker modal:

Search bar (subject/from) + date filter + quick filter “From candidate”.

Result list with checkboxes + minimal metadata.

Footer: Attach (Link) | Attach (Snapshot) (system default selectable).

Timeline card (after action):

“Attached emails (N)” → list subject/from/date; buttons: Open in Gmail/Outlook | View Snapshot (if stored).

Empty state: “Connect your email to attach evidence.”

8) Permissions

New granular permission (if you want to guard it):

workflow.evidence.email.attach (who can attach email evidence)

workflow.evidence.email.view (who can view)

Usually assign both to departments that own the step.

9) API endpoints (names only;)

POST /api/email-accounts/connect/{provider} → OAuth redirect/init

GET /api/email/search?provider=...&q=...&date_from=... → list messages (current user’s mailbox)

POST /api/step-actions/{id}/evidence/email → attach selected message_ids (mode=link|snapshot)

GET /api/evidence/{evidenceId} → metadata / signed URL to snapshot

POST /api/email-ingest/{token} → (Phase 0 forward‑to‑ingest) receive EML and attach

10) Acceptance criteria

Admin/department user can attach one or more emails to any step action (Approve/Reject/Return).

Evidence cards show Subject/From/Date and either a provider link or snapshot viewer.

Audit log records who attached what, when, and how (link/snapshot), with hashes for integrity.

Without OAuth, forward‑to‑ingest still works (Phase 0).

With OAuth, search & pick works for Gmail and Outlook; minimal scopes only.

Revoking OAuth disables search; existing evidence remains viewable (snapshots) or linkable (if mailbox access remains).

11) QA scenarios

Attach (Link) & Attach (Snapshot) both succeed; snapshot stores body + attachments and hashes.

Revoke OAuth → search disabled; existing evidence intact.

Email deleted in mailbox: Link may 404; Snapshot remains.

Large attachments: snapshot policy (size limit) respected; user warned if skipped.

Permissions enforced: user without evidence permission can’t attach or view.

Forward‑to‑ingest: forwarded email lands on the correct step action; headers parsed correctly.

12) Implementation notes

Build a provider adapter interface: GmailAdapter / OutlookAdapter both expose search, getMessage, downloadAttachment.

Keep a strict timeout & retries for provider calls; degrade gracefully (fallback to link‑only if snapshot fails).

Sanitize HTML to safe subset; store both text/html (sanitized) and text/plain for search (if allowed).

Add a config toggle: EMAIL_EVIDENCE_MODE = link_only | snapshot | both and a max_attachment_bytes threshold.