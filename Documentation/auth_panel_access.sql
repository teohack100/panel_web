CREATE TABLE IF NOT EXISTS `auth_panel_access` (
  `user_id` INT(11) NOT NULL,
  `is_enabled` TINYINT(1) NOT NULL DEFAULT 0,
  `allow_zero_credits` TINYINT(1) NOT NULL DEFAULT 0,
  `plan_code` VARCHAR(64) NOT NULL DEFAULT 'pending',
  `lock_reason` VARCHAR(191) NOT NULL DEFAULT 'pending_activation',
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  `last_granted_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `idx_panel_enabled` (`is_enabled`, `updated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Activar acceso completo para un usuario especifico:
-- UPDATE auth_panel_access
-- SET is_enabled=1, lock_reason='active', plan_code='reseller', updated_at=NOW(), last_granted_at=NOW()
-- WHERE user_id=10706;

-- Bloquear nuevamente:
-- UPDATE auth_panel_access
-- SET is_enabled=0, lock_reason='manual_lock', updated_at=NOW()
-- WHERE user_id=10706;
