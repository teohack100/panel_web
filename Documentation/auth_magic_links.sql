CREATE TABLE IF NOT EXISTS auth_magic_links (
  id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) NOT NULL,
  user_email VARCHAR(190) NOT NULL,
  token_hash CHAR(64) NOT NULL,
  created_ip VARCHAR(64) NOT NULL DEFAULT '',
  created_at DATETIME NOT NULL,
  expires_at DATETIME NOT NULL,
  used_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_token_hash (token_hash),
  KEY idx_magic_user (user_id),
  KEY idx_magic_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

