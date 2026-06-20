CREATE TABLE IF NOT EXISTS auth_oauth_audit (
  id INT(11) NOT NULL AUTO_INCREMENT,
  provider VARCHAR(20) NOT NULL,
  event_name VARCHAR(40) NOT NULL,
  status VARCHAR(20) NOT NULL,
  details VARCHAR(255) NOT NULL DEFAULT '',
  request_ip VARCHAR(64) NOT NULL DEFAULT '',
  request_ua VARCHAR(255) NOT NULL DEFAULT '',
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_audit_provider (provider),
  KEY idx_audit_event (event_name),
  KEY idx_audit_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

