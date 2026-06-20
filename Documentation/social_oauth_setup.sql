-- 1) Google (replace placeholders)
UPDATE auth_oauth_providers
SET
  enabled = 1,
  client_id = 'GOOGLE_CLIENT_ID.apps.googleusercontent.com',
  client_secret = 'GOOGLE_CLIENT_SECRET',
  scope = 'openid email profile',
  updated_at = NOW()
WHERE provider = 'google';

-- 2) Facebook (replace placeholders)
UPDATE auth_oauth_providers
SET
  enabled = 1,
  client_id = 'FACEBOOK_APP_ID',
  client_secret = 'FACEBOOK_APP_SECRET',
  scope = 'email public_profile',
  updated_at = NOW()
WHERE provider = 'facebook';

-- 3) Optional disable providers
-- UPDATE auth_oauth_providers SET enabled=0, updated_at=NOW() WHERE provider='google';
-- UPDATE auth_oauth_providers SET enabled=0, updated_at=NOW() WHERE provider='facebook';

-- 4) Security policy (recommended)
-- Block OAuth on the master control host (control.programmit.com)
INSERT INTO saas_settings (setting_key, setting_value, created_at, updated_at)
VALUES ('social_oauth_block_on_control_host', '1', NOW(), NOW())
ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value), updated_at=NOW();

-- Disable social auto-signup (only existing users can login with social)
INSERT INTO saas_settings (setting_key, setting_value, created_at, updated_at)
VALUES ('social_oauth_signup_enabled', '0', NOW(), NOW())
ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value), updated_at=NOW();
