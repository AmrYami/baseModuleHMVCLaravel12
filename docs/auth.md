# Authentication

## Flows
- **Login / Logout**: Standard Fortify-powered login with rate limiting and session guard.
- **Registration**: Enabled; subject to rate limits (see Security).
- **Email Verification**: Required; verification routes/views are active.
- **Password Reset**: Standard Fortify reset via email.
- **Two-Factor Authentication (2FA)**: Enabled with recovery codes; enforced via Fortify features.

## Configuration
- Environment:
  - `APP_URL` — base URL (adjust per environment).
  - `MAIL_*` — SMTP settings for all auth emails (verification, reset, 2FA).
- Fortify & Jetstream:
  - `config/fortify.php` — features, limiters.
  - `config/jetstream.php` — teams, account deletion, etc.

## Rate Limits
- Login: `Limit::perMinute(5)` per email+IP.
- 2FA challenge: `Limit::perMinute(5)` per login session.
- Forgot password: `3/min` and `10/hour` per email+IP.
- Registration: `3/min` and `10/hour` per email+IP.

## Usage
- **Verification/2FA emails**: Require valid `MAIL_*` settings; run `php artisan config:clear` after changes.
- **2FA enrollment**: Available in user security settings; recovery codes can be regenerated.***
