# Security & CSP

## Content Security Policy (CSP)
- Enforced via `Spatie\Csp\AddCspHeaders` with nonces on inline scripts/styles.
- Allowed sources: self, `code.jquery.com` (scripts), `fonts.googleapis.com` (styles), `fonts.gstatic.com` + data (fonts), self + data (images).
- Report endpoint: `POST /api/csp-report` (configured via `CSP_REPORT_URI`); violations logged.
- Nonces: added to all inline scripts/styles that remain; no `unsafe-inline` is used.

## Security Headers
- Global middleware adds:
  - `X-Frame-Options: SAMEORIGIN`
  - `X-Content-Type-Options: nosniff`
  - `Referrer-Policy: strict-origin-when-cross-origin`
  - `Strict-Transport-Security` when HTTPS is used.

## HTTPS & Cookies
- Middleware `ForceHttps` can redirect HTTP→HTTPS when `FORCE_HTTPS=true`.
- Env flags: `HTTPS` (for app helpers), `FORCE_HTTPS`, `APP_URL`.
- Sessions: secure/HTTP-only/same-site driven by env; `SESSION_SECURE_COOKIE` is false in local, set true in production.

## Tokens & Expiry
- Sanctum tokens expire (`SANCTUM_EXPIRATION`, default 30 days) and are pruned nightly (`sanctum:prune-expired`).
- Password reset tokens cleared nightly (`auth:clear-resets`).

## Rate Limiting
- Login, 2FA, forgot password, registration throttled (see `docs/auth.md`).

## Upload Validation
- See `docs/uploads.md` for per-field limits and mime rules.
