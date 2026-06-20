CREATE TABLE IF NOT EXISTS auth_oauth_sessions (
  id INT(11) NOT NULL AUTO_INCREMENT,
  provider VARCHAR(20) NOT NULL,
  state_hash CHAR(64) NOT NULL,
  pkce_verifier VARCHAR(191) NOT NULL DEFAULT '',
  redirect_uri VARCHAR(500) NOT NULL DEFAULT '',
  request_ip VARCHAR(64) NOT NULL DEFAULT '',
  request_ua VARCHAR(255) NOT NULL DEFAULT '',
  created_at DATETIME NOT NULL,
  expires_at DATETIME NOT NULL,
  consumed_at DATETIME DEFAULT NULL,
  fail_count TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_state_hash (state_hash),
  KEY idx_oauth_provider_exp (provider, expires_at),
  KEY idx_oauth_consumed (consumed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

