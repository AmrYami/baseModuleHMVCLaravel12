# Authentication

## Flows
- **Login / Logout**: Standard Fortify-powered login with rate limiting and session guard.
- **Registration**: Enabled; subject to rate limits (see Security).
- **Email Verification**: Required; verification routes/views are active.
- **Password Reset**: Standard Fortify reset via email.
- **Two-Factor Authentication (2FA)**: Enabled with authenticator-app TOTP codes and recovery codes; enforced via Fortify features.
- **Mobile API Authentication**: Laravel Passport issues bearer tokens for API clients.

## Configuration
- Environment:
  - `APP_URL` — base URL (adjust per environment).
  - `MAIL_*` — SMTP settings for verification and password reset emails.
  - `PASSPORT_PRIVATE_KEY` / `PASSPORT_PUBLIC_KEY` — optional Passport key material when keys are supplied from the environment.
- Fortify & Jetstream:
  - `config/fortify.php` — features, limiters.
  - `config/jetstream.php` — teams, account deletion, etc.
  - `config/auth.php` — `api` guard uses Passport.

## Rate Limits
- Login: `Limit::perMinute(5)` per email+IP.
- 2FA challenge: `Limit::perMinute(5)` per login session.
- Forgot password: `3/min` and `10/hour` per email+IP.
- Registration: `3/min` and `10/hour` per email+IP.

## Usage
- **Verification/reset emails**: Require valid `MAIL_*` settings; run `php artisan config:clear` after changes.
- **2FA enrollment**: Available in user security settings. Users scan the TOTP secret with an authenticator app and can regenerate recovery codes.
- **Passport API tokens**: API clients authenticate with `Authorization: Bearer <token>` against Passport-protected routes. Tokens are issued by the mobile API login flow and revoked on logout.
