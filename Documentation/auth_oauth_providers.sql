CREATE TABLE IF NOT EXISTS auth_oauth_providers (
  provider VARCHAR(20) NOT NULL,
  enabled TINYINT(1) NOT NULL DEFAULT 0,
  client_id VARCHAR(255) NOT NULL DEFAULT '',
  client_secret VARCHAR(255) NOT NULL DEFAULT '',
  scope VARCHAR(255) NOT NULL DEFAULT '',
  created_at DATETIME NOT NULL,
  updated_at DATETIME DEFAULT NULL,
  PRIMARY KEY (provider)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO auth_oauth_providers
  (provider, enabled, client_id, client_secret, scope, created_at, updated_at)
VALUES
  ('google', 0, '', '', 'openid email profile', NOW(), NOW()),
  ('facebook', 0, '', '', 'email public_profile', NOW(), NOW()),
  ('apple', 0, '', '', 'name email', NOW(), NOW());

