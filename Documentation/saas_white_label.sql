-- PROGRAMMIT SaaS White-Label Core Schema
-- Ejecutar en la base central (ej: programm_panel).

CREATE TABLE IF NOT EXISTS saas_plans (
    id INT(11) NOT NULL AUTO_INCREMENT,
    plan_code VARCHAR(64) NOT NULL,
    plan_name VARCHAR(120) NOT NULL,
    description VARCHAR(255) NOT NULL DEFAULT '',
    monthly_price_usd DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    setup_fee_usd DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    credit_price_usd DECIMAL(12,4) NOT NULL DEFAULT 1.0000,
    included_credits INT(11) NOT NULL DEFAULT 0,
    panel_limit INT(11) NOT NULL DEFAULT 1,
    user_limit INT(11) NOT NULL DEFAULT 1,
    method_limit INT(11) NOT NULL DEFAULT 3,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    is_public TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_plan_code (plan_code),
    KEY idx_plan_active (is_active, is_public)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS saas_tenants (
    id INT(11) NOT NULL AUTO_INCREMENT,
    tenant_key VARCHAR(64) NOT NULL,
    owner_user_id INT(11) NOT NULL DEFAULT 1,
    plan_id INT(11) NOT NULL DEFAULT 0,
    display_name VARCHAR(140) NOT NULL,
    brand_name VARCHAR(140) NOT NULL DEFAULT '',
    support_email VARCHAR(191) NOT NULL DEFAULT '',
    status VARCHAR(24) NOT NULL DEFAULT 'trial',
    credits_balance INT(11) NOT NULL DEFAULT 0,
    monthly_price_usd DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    credit_price_usd DECIMAL(12,4) NOT NULL DEFAULT 0.0000,
    default_currency VARCHAR(8) NOT NULL DEFAULT 'USD',
    timezone VARCHAR(64) NOT NULL DEFAULT 'UTC',
    notes MEDIUMTEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT NULL,
    last_seen_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_tenant_key (tenant_key),
    KEY idx_tenant_owner (owner_user_id),
    KEY idx_tenant_plan (plan_id),
    KEY idx_tenant_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS saas_tenant_domains (
    id INT(11) NOT NULL AUTO_INCREMENT,
    tenant_id INT(11) NOT NULL,
    hostname VARCHAR(191) NOT NULL,
    is_primary TINYINT(1) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    verified_at DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_hostname (hostname),
    KEY idx_domain_tenant (tenant_id, is_active, is_primary)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS saas_tenant_branding (
    tenant_id INT(11) NOT NULL,
    logo_url VARCHAR(255) NOT NULL DEFAULT '',
    favicon_url VARCHAR(255) NOT NULL DEFAULT '',
    primary_color VARCHAR(16) NOT NULL DEFAULT '#2fbde5',
    accent_color VARCHAR(16) NOT NULL DEFAULT '#95f100',
    background_color VARCHAR(16) NOT NULL DEFAULT '#132744',
    custom_css MEDIUMTEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT NULL,
    PRIMARY KEY (tenant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS saas_settings (
    id INT(11) NOT NULL AUTO_INCREMENT,
    setting_key VARCHAR(64) NOT NULL,
    setting_value MEDIUMTEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_saas_setting_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS saas_sync_logs (
    id INT(11) NOT NULL AUTO_INCREMENT,
    sync_source VARCHAR(32) NOT NULL DEFAULT 'auto',
    started_at DATETIME NOT NULL,
    ended_at DATETIME DEFAULT NULL,
    status VARCHAR(24) NOT NULL DEFAULT 'running',
    summary_json MEDIUMTEXT NULL,
    error_text MEDIUMTEXT NULL,
    PRIMARY KEY (id),
    KEY idx_sync_started (started_at),
    KEY idx_sync_status (status, ended_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Extensiones backend para scoping por tenant
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS tenant_id INT(11) NOT NULL DEFAULT 0,
    ADD COLUMN IF NOT EXISTS is_tenant_owner TINYINT(1) NOT NULL DEFAULT 0,
    ADD INDEX IF NOT EXISTS idx_users_tenant (tenant_id),
    ADD INDEX IF NOT EXISTS idx_users_tenant_owner (is_tenant_owner);

ALTER TABLE finance_recharges
    ADD COLUMN IF NOT EXISTS tenant_id INT(11) NOT NULL DEFAULT 0 AFTER user_id,
    ADD COLUMN IF NOT EXISTS owner_user_id INT(11) NOT NULL DEFAULT 0 AFTER tenant_id,
    ADD INDEX IF NOT EXISTS idx_recharge_tenant (tenant_id, created_at),
    ADD INDEX IF NOT EXISTS idx_recharge_owner (owner_user_id, created_at);

ALTER TABLE finance_wallet_logs
    ADD COLUMN IF NOT EXISTS tenant_id INT(11) NOT NULL DEFAULT 0 AFTER user_id,
    ADD INDEX IF NOT EXISTS idx_wallet_tenant (tenant_id, created_at);

-- Ajustes de control plane
INSERT INTO saas_settings (setting_key, setting_value, created_at, updated_at) VALUES
    ('saas_control_host', 'control.programmit.com', NOW(), NOW()),
    ('saas_default_panel_host', 'panel.programmit.com', NOW(), NOW()),
    ('saas_auto_sync_enabled', '1', NOW(), NOW()),
    ('saas_allow_local_control', '1', NOW(), NOW())
ON DUPLICATE KEY UPDATE
    setting_value = VALUES(setting_value),
    updated_at = NOW();

-- Planes base
INSERT INTO saas_plans
    (plan_code, plan_name, description, monthly_price_usd, setup_fee_usd, credit_price_usd,
     included_credits, panel_limit, user_limit, method_limit, is_active, is_public, created_at, updated_at)
VALUES
    ('starter', 'Starter', 'Plan base para comenzar a vender paneles.', 19.00, 0.00, 1.0000, 25, 1, 150, 3, 1, 1, NOW(), NOW()),
    ('pro', 'Pro', 'Para revendedores con mayor volumen.', 59.00, 0.00, 0.9500, 100, 5, 1000, 10, 1, 1, NOW(), NOW()),
    ('enterprise', 'Enterprise', 'Plan alto rendimiento para marca blanca masiva.', 149.00, 0.00, 0.9000, 300, 25, 5000, 50, 1, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    plan_name = VALUES(plan_name),
    description = VALUES(description),
    monthly_price_usd = VALUES(monthly_price_usd),
    setup_fee_usd = VALUES(setup_fee_usd),
    credit_price_usd = VALUES(credit_price_usd),
    included_credits = VALUES(included_credits),
    panel_limit = VALUES(panel_limit),
    user_limit = VALUES(user_limit),
    method_limit = VALUES(method_limit),
    is_active = VALUES(is_active),
    is_public = VALUES(is_public),
    updated_at = NOW();

-- Tenant principal (ajusta hostname si usas otro)
INSERT INTO saas_tenants
    (tenant_key, owner_user_id, plan_id, display_name, brand_name, support_email, status, credits_balance,
     monthly_price_usd, credit_price_usd, default_currency, timezone, notes, created_at, updated_at)
SELECT
    'programmit', 1,
    COALESCE((SELECT id FROM saas_plans WHERE plan_code='enterprise' LIMIT 1), 0),
    'PROGRAMMIT', 'PROGRAMMIT', '', 'active', 0, 0.00, 0.0000, 'USD', 'UTC',
    'Tenant principal autogenerado por schema SQL', NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM saas_tenants WHERE tenant_key='programmit'
);

INSERT INTO saas_tenant_domains
    (tenant_id, hostname, is_primary, is_active, verified_at, created_at, updated_at)
SELECT
    t.id, 'panel.programmit.com', 1, 1, NOW(), NOW(), NOW()
FROM saas_tenants t
WHERE t.tenant_key='programmit'
  AND NOT EXISTS (
      SELECT 1 FROM saas_tenant_domains d
      WHERE d.hostname='panel.programmit.com'
  );
