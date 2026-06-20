# Social OAuth Setup (Google/Facebook)

## 1) Tables
Execute these SQL files (or just click social login once, backend auto-creates them):

- `Documentation/auth_social_accounts.sql`
- `Documentation/auth_oauth_providers.sql`
- `Documentation/auth_oauth_sessions.sql`
- `Documentation/auth_oauth_audit.sql`

## 2) Set provider keys
Run `Documentation/social_oauth_setup.sql` and replace placeholders.

## 3) Callback URLs
Register these exact URLs in provider consoles:

- Google: `https://TU_DOMINIO/index.php?p=social-callback&provider=google`
- Facebook: `https://TU_DOMINIO/index.php?p=social-callback&provider=facebook`

If you see `redirect_uri_mismatch`, the callback URL in Google/Facebook does not exactly match the current host and path.

## 4) Optional environment variables
Higher priority than DB/file:

- `PM_SOCIAL_GOOGLE_ENABLED`
- `PM_SOCIAL_GOOGLE_CLIENT_ID`
- `PM_SOCIAL_GOOGLE_CLIENT_SECRET`
- `PM_SOCIAL_GOOGLE_SCOPE`
- `PM_SOCIAL_FACEBOOK_ENABLED`
- `PM_SOCIAL_FACEBOOK_CLIENT_ID`
- `PM_SOCIAL_FACEBOOK_CLIENT_SECRET`
- `PM_SOCIAL_FACEBOOK_SCOPE`

## Security implemented

- OAuth state stored server-side in DB (one-time, 10 min expiry).
- Google PKCE (S256) enabled.
- Callback replay protection (`consumed_at`).
- Audit log table (`auth_oauth_audit`).
- Auth cookies set with `HttpOnly` + `SameSite=Lax` (+ `Secure` on HTTPS).
- Host policy support:
  - `saas_settings.social_oauth_block_on_control_host=1` blocks OAuth on `control` host.
  - `saas_settings.social_oauth_signup_enabled=0` disables auto-create via social login.
