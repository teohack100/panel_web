CREATE TABLE IF NOT EXISTS auth_social_accounts (
  id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) NOT NULL,
  provider VARCHAR(20) NOT NULL,
  provider_uid VARCHAR(191) NOT NULL,
  provider_email VARCHAR(191) NOT NULL DEFAULT '',
  provider_name VARCHAR(191) NOT NULL DEFAULT '',
  created_at DATETIME NOT NULL,
  last_login_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_provider_uid (provider, provider_uid),
  KEY idx_social_user (user_id),
  KEY idx_social_provider (provider)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

