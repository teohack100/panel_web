-- Optional hardening at DB layer (recommended in production)
-- This keeps auth_panel_access in sync when rows are inserted in users.

DELIMITER $$

CREATE TRIGGER trg_users_after_insert_panel_access
AFTER INSERT ON users
FOR EACH ROW
BEGIN
	INSERT INTO auth_panel_access
		(user_id, is_enabled, allow_zero_credits, plan_code, lock_reason, created_at, updated_at, last_granted_at)
	VALUES
		(
			NEW.user_id,
			CASE
				WHEN NEW.user_id = 1 THEN 1
				WHEN NEW.user_level IN ('superadmin','administrator','subadmin','reseller','subreseller') THEN 1
				WHEN NEW.credits > 0 THEN 1
				ELSE 0
			END,
			CASE
				WHEN NEW.user_id = 1 THEN 1
				WHEN NEW.user_level IN ('superadmin','administrator','subadmin','reseller','subreseller') THEN 1
				ELSE 0
			END,
			CASE
				WHEN NEW.user_id = 1 OR NEW.user_level IN ('superadmin','administrator','subadmin','reseller','subreseller') OR NEW.credits > 0 THEN 'enabled'
				ELSE 'pending'
			END,
			CASE
				WHEN NEW.user_id = 1 OR NEW.user_level IN ('superadmin','administrator','subadmin','reseller','subreseller') OR NEW.credits > 0 THEN 'active'
				ELSE 'pending_activation'
			END,
			NOW(),
			NOW(),
			CASE
				WHEN NEW.user_id = 1 OR NEW.user_level IN ('superadmin','administrator','subadmin','reseller','subreseller') OR NEW.credits > 0 THEN NOW()
				ELSE NULL
			END
		)
	ON DUPLICATE KEY UPDATE
		updated_at = NOW();
END$$

DELIMITER ;
