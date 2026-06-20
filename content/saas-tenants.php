<?php
chkSession();

if (!programmit_saas_is_platform_admin($user_id_2, $user_level_2)) {
    header("Location: ".$db->base_url()."index.php?p=dashboard");
    exit;
}

programmit_saas_ensure_tables($db);
$controlHost = programmit_saas_get_control_host($db);
if (!programmit_saas_can_manage_from_current_host($db)) {
    header("Location: https://" . $controlHost . "/index.php?p=saas-tenants");
    exit;
}

$saas_tenant_error = '';
$saas_tenant_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_tenant'])) {
    $tenantId = isset($_POST['tenant_id']) ? (int)$_POST['tenant_id'] : 0;
    $tenantKey = strtolower(trim((string)$_POST['tenant_key']));
    $ownerUserId = isset($_POST['owner_user_id']) ? (int)$_POST['owner_user_id'] : 1;
    $planId = isset($_POST['plan_id']) ? (int)$_POST['plan_id'] : 0;
    $displayName = trim((string)$_POST['display_name']);
    $brandName = trim((string)$_POST['brand_name']);
    $supportEmail = trim((string)$_POST['support_email']);
    $status = programmit_saas_status_normalize(isset($_POST['status']) ? $_POST['status'] : 'trial');
    $creditsBalance = isset($_POST['credits_balance']) ? (int)$_POST['credits_balance'] : 0;
    $monthlyPrice = isset($_POST['monthly_price_usd']) ? (float)$_POST['monthly_price_usd'] : 0;
    $creditPrice = isset($_POST['credit_price_usd']) ? (float)$_POST['credit_price_usd'] : 0;
    $defaultCurrency = strtoupper(trim((string)$_POST['default_currency']));
    $timezone = trim((string)$_POST['timezone']);
    $notes = trim((string)$_POST['notes']);

    $logoUrl = trim((string)$_POST['logo_url']);
    $faviconUrl = trim((string)$_POST['favicon_url']);
    $primaryColor = trim((string)$_POST['primary_color']);
    $accentColor = trim((string)$_POST['accent_color']);
    $backgroundColor = trim((string)$_POST['background_color']);
    $customCss = trim((string)$_POST['custom_css']);

    if (!programmit_saas_valid_key($tenantKey)) {
        $saas_tenant_error = 'Tenant key invalido (usa a-z, 0-9, guion o guion bajo).';
    } elseif ($displayName === '') {
        $saas_tenant_error = 'Nombre visible del tenant obligatorio.';
    } elseif ($ownerUserId <= 0) {
        $saas_tenant_error = 'Owner user_id invalido.';
    } elseif ($planId < 0) {
        $saas_tenant_error = 'Plan invalido.';
    } elseif ($supportEmail !== '' && !filter_var($supportEmail, FILTER_VALIDATE_EMAIL)) {
        $saas_tenant_error = 'Email de soporte invalido.';
    } elseif ($creditsBalance < 0 || $monthlyPrice < 0 || $creditPrice < 0) {
        $saas_tenant_error = 'Valores economicos invalidos.';
    } elseif ($defaultCurrency !== '' && !preg_match('/^[A-Z]{3,8}$/', $defaultCurrency)) {
        $saas_tenant_error = 'Moneda invalida. Ejemplo: USD, BOB.';
    } elseif ($timezone !== '' && strlen($timezone) > 64) {
        $saas_tenant_error = 'Timezone demasiado largo.';
    } elseif ($primaryColor !== '' && !preg_match('/^#[0-9a-fA-F]{3,8}$/', $primaryColor)) {
        $saas_tenant_error = 'Color primario invalido.';
    } elseif ($accentColor !== '' && !preg_match('/^#[0-9a-fA-F]{3,8}$/', $accentColor)) {
        $saas_tenant_error = 'Color accent invalido.';
    } elseif ($backgroundColor !== '' && !preg_match('/^#[0-9a-fA-F]{3,8}$/', $backgroundColor)) {
        $saas_tenant_error = 'Color de fondo invalido.';
    } else {
        $dupWhere = ($tenantId > 0)
            ? " AND id<>'".$db->SanitizeForSQL($tenantId)."'"
            : "";
        $dupQry = $db->sql_query("SELECT id
            FROM saas_tenants
            WHERE tenant_key='".$db->SanitizeForSQL($tenantKey)."'
            ".$dupWhere."
            LIMIT 1");
        if ($dupQry && $db->sql_numrows($dupQry) > 0) {
            $saas_tenant_error = 'Ese tenant key ya existe.';
        } else {
            if ($defaultCurrency === '') {
                $defaultCurrency = 'USD';
            }
            if ($timezone === '') {
                $timezone = 'UTC';
            }

            if ($tenantId > 0) {
                $ok = $db->sql_query("UPDATE saas_tenants
                    SET tenant_key='".$db->SanitizeForSQL($tenantKey)."',
                        owner_user_id='".$db->SanitizeForSQL($ownerUserId)."',
                        plan_id='".$db->SanitizeForSQL($planId)."',
                        display_name='".$db->SanitizeForSQL($displayName)."',
                        brand_name='".$db->SanitizeForSQL($brandName)."',
                        support_email='".$db->SanitizeForSQL($supportEmail)."',
                        status='".$db->SanitizeForSQL($status)."',
                        credits_balance='".$db->SanitizeForSQL($creditsBalance)."',
                        monthly_price_usd='".$db->SanitizeForSQL(number_format($monthlyPrice, 2, '.', ''))."',
                        credit_price_usd='".$db->SanitizeForSQL(number_format($creditPrice, 4, '.', ''))."',
                        default_currency='".$db->SanitizeForSQL($defaultCurrency)."',
                        timezone='".$db->SanitizeForSQL($timezone)."',
                        notes='".$db->SanitizeForSQL($notes)."',
                        updated_at=NOW()
                    WHERE id='".$db->SanitizeForSQL($tenantId)."'
                    LIMIT 1");
                if (!$ok) {
                    $saas_tenant_error = 'No se pudo actualizar el tenant.';
                } else {
                    $saas_tenant_success = 'Tenant actualizado correctamente.';
                }
            } else {
                $ok = $db->sql_query("INSERT INTO saas_tenants
                    (tenant_key, owner_user_id, plan_id, display_name, brand_name, support_email, status, credits_balance,
                     monthly_price_usd, credit_price_usd, default_currency, timezone, notes, created_at, updated_at)
                    VALUES
                    ('".$db->SanitizeForSQL($tenantKey)."',
                     '".$db->SanitizeForSQL($ownerUserId)."',
                     '".$db->SanitizeForSQL($planId)."',
                     '".$db->SanitizeForSQL($displayName)."',
                     '".$db->SanitizeForSQL($brandName)."',
                     '".$db->SanitizeForSQL($supportEmail)."',
                     '".$db->SanitizeForSQL($status)."',
                     '".$db->SanitizeForSQL($creditsBalance)."',
                     '".$db->SanitizeForSQL(number_format($monthlyPrice, 2, '.', ''))."',
                     '".$db->SanitizeForSQL(number_format($creditPrice, 4, '.', ''))."',
                     '".$db->SanitizeForSQL($defaultCurrency)."',
                     '".$db->SanitizeForSQL($timezone)."',
                     '".$db->SanitizeForSQL($notes)."',
                     NOW(),
                     NOW())");
                if (!$ok) {
                    $saas_tenant_error = 'No se pudo crear el tenant.';
                } else {
                    $saas_tenant_success = 'Tenant creado correctamente.';
                }
            }

            if ($saas_tenant_error === '') {
                if ($tenantId <= 0) {
                    $idQry = $db->sql_query("SELECT id FROM saas_tenants
                        WHERE tenant_key='".$db->SanitizeForSQL($tenantKey)."'
                        LIMIT 1");
                    $idRow = $db->sql_fetchrow($idQry);
                    $tenantId = $idRow ? (int)$idRow['id'] : 0;
                }

                if ($tenantId > 0) {
                    if (method_exists($db, 'is_pgsql') && $db->is_pgsql()) {
                        $db->sql_query("INSERT INTO saas_tenant_branding
                            (tenant_id, logo_url, favicon_url, primary_color, accent_color, background_color, custom_css, created_at, updated_at)
                            VALUES
                            ('".$db->SanitizeForSQL($tenantId)."',
                             '".$db->SanitizeForSQL($logoUrl)."',
                             '".$db->SanitizeForSQL($faviconUrl)."',
                             '".$db->SanitizeForSQL($primaryColor !== '' ? $primaryColor : '#2fbde5')."',
                             '".$db->SanitizeForSQL($accentColor !== '' ? $accentColor : '#95f100')."',
                             '".$db->SanitizeForSQL($backgroundColor !== '' ? $backgroundColor : '#132744')."',
                             '".$db->SanitizeForSQL($customCss)."',
                             NOW(),
                             NOW())
                            ON CONFLICT (tenant_id) DO UPDATE
                            SET logo_url=EXCLUDED.logo_url,
                                favicon_url=EXCLUDED.favicon_url,
                                primary_color=EXCLUDED.primary_color,
                                accent_color=EXCLUDED.accent_color,
                                background_color=EXCLUDED.background_color,
                                custom_css=EXCLUDED.custom_css,
                                updated_at=NOW()");
                    } else {
                        $db->sql_query("INSERT INTO saas_tenant_branding
                            (tenant_id, logo_url, favicon_url, primary_color, accent_color, background_color, custom_css, created_at, updated_at)
                            VALUES
                            ('".$db->SanitizeForSQL($tenantId)."',
                             '".$db->SanitizeForSQL($logoUrl)."',
                             '".$db->SanitizeForSQL($faviconUrl)."',
                             '".$db->SanitizeForSQL($primaryColor !== '' ? $primaryColor : '#2fbde5')."',
                             '".$db->SanitizeForSQL($accentColor !== '' ? $accentColor : '#95f100')."',
                             '".$db->SanitizeForSQL($backgroundColor !== '' ? $backgroundColor : '#132744')."',
                             '".$db->SanitizeForSQL($customCss)."',
                             NOW(),
                             NOW())
                            ON DUPLICATE KEY UPDATE
                                logo_url=VALUES(logo_url),
                                favicon_url=VALUES(favicon_url),
                                primary_color=VALUES(primary_color),
                                accent_color=VALUES(accent_color),
                                background_color=VALUES(background_color),
                                custom_css=VALUES(custom_css),
                                updated_at=NOW()");
                    }
                }
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_tenant_domain'])) {
    $tenantId = isset($_POST['domain_tenant_id']) ? (int)$_POST['domain_tenant_id'] : 0;
    $hostname = programmit_saas_normalize_host(isset($_POST['hostname']) ? $_POST['hostname'] : '');
    $isPrimary = isset($_POST['is_primary']) ? 1 : 0;
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    if ($tenantId <= 0) {
        $saas_tenant_error = 'Tenant invalido para dominio.';
    } elseif (!programmit_saas_valid_hostname($hostname)) {
        $saas_tenant_error = 'Hostname invalido.';
    } else {
        $hostQry = $db->sql_query("SELECT id
            FROM saas_tenant_domains
            WHERE hostname='".$db->SanitizeForSQL($hostname)."'
            LIMIT 1");
        $hostRow = $db->sql_fetchrow($hostQry);
        if ($hostRow) {
            $saas_tenant_error = 'Ese hostname ya esta registrado.';
        } else {
            if ($isPrimary === 1) {
                $db->sql_query("UPDATE saas_tenant_domains
                    SET is_primary='0', updated_at=NOW()
                    WHERE tenant_id='".$db->SanitizeForSQL($tenantId)."'");
            }

            $db->sql_query("INSERT INTO saas_tenant_domains
                (tenant_id, hostname, is_primary, is_active, verified_at, created_at, updated_at)
                VALUES
                ('".$db->SanitizeForSQL($tenantId)."',
                 '".$db->SanitizeForSQL($hostname)."',
                 '".$db->SanitizeForSQL($isPrimary)."',
                 '".$db->SanitizeForSQL($isActive)."',
                 NOW(),
                 NOW(),
                 NOW())");
            $saas_tenant_success = 'Dominio agregado al tenant.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_primary_domain'])) {
    $domainId = isset($_POST['domain_id']) ? (int)$_POST['domain_id'] : 0;
    if ($domainId > 0) {
        $qry = $db->sql_query("SELECT id, tenant_id
            FROM saas_tenant_domains
            WHERE id='".$db->SanitizeForSQL($domainId)."'
            LIMIT 1");
        $row = $db->sql_fetchrow($qry);
        if ($row) {
            $tenantId = (int)$row['tenant_id'];
            $db->sql_query("UPDATE saas_tenant_domains
                SET is_primary='0', updated_at=NOW()
                WHERE tenant_id='".$db->SanitizeForSQL($tenantId)."'");
            $db->sql_query("UPDATE saas_tenant_domains
                SET is_primary='1', is_active='1', updated_at=NOW()
                WHERE id='".$db->SanitizeForSQL($domainId)."'
                LIMIT 1");
            $saas_tenant_success = 'Dominio principal actualizado.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_domain'])) {
    $domainId = isset($_POST['domain_id']) ? (int)$_POST['domain_id'] : 0;
    if ($domainId > 0) {
        $db->sql_query("UPDATE saas_tenant_domains
            SET is_active = IF(is_active=1,0,1), updated_at=NOW()
            WHERE id='".$db->SanitizeForSQL($domainId)."'
            LIMIT 1");
        $saas_tenant_success = 'Estado del dominio actualizado.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['suspend_tenant'])) {
    $tenantId = isset($_POST['suspend_tenant_id']) ? (int)$_POST['suspend_tenant_id'] : 0;
    if ($tenantId > 0) {
        $rowQry = $db->sql_query("SELECT tenant_key FROM saas_tenants
            WHERE id='".$db->SanitizeForSQL($tenantId)."'
            LIMIT 1");
        $row = $db->sql_fetchrow($rowQry);
        if ($row && strtolower((string)$row['tenant_key']) === 'programmit') {
            $saas_tenant_error = 'No puedes suspender el tenant principal programmit.';
        } else {
            $db->sql_query("UPDATE saas_tenants
                SET status='suspended', updated_at=NOW()
                WHERE id='".$db->SanitizeForSQL($tenantId)."'
                LIMIT 1");
            $saas_tenant_success = 'Tenant suspendido.';
        }
    }
}

$editTenantId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editTenant = null;
if ($editTenantId > 0) {
    $editQry = $db->sql_query("SELECT
        t.id, t.tenant_key, t.owner_user_id, t.plan_id, t.display_name, t.brand_name, t.support_email,
        t.status, t.credits_balance, t.monthly_price_usd, t.credit_price_usd, t.default_currency, t.timezone, t.notes,
        b.logo_url, b.favicon_url, b.primary_color, b.accent_color, b.background_color, b.custom_css
        FROM saas_tenants t
        LEFT JOIN saas_tenant_branding b ON b.tenant_id=t.id
        WHERE t.id='".$db->SanitizeForSQL($editTenantId)."'
        LIMIT 1");
    $editTenant = $db->sql_fetchrow($editQry);
}

if (!$editTenant) {
    $editTenant = array(
        'id' => 0,
        'tenant_key' => '',
        'owner_user_id' => 1,
        'plan_id' => 0,
        'display_name' => '',
        'brand_name' => '',
        'support_email' => '',
        'status' => 'trial',
        'credits_balance' => 0,
        'monthly_price_usd' => 0,
        'credit_price_usd' => 0,
        'default_currency' => 'USD',
        'timezone' => 'UTC',
        'notes' => '',
        'logo_url' => '',
        'favicon_url' => '',
        'primary_color' => '#2fbde5',
        'accent_color' => '#95f100',
        'background_color' => '#132744',
        'custom_css' => ''
    );
}

$planRows = programmit_saas_list_plans($db);
$tenantRows = programmit_saas_list_tenants($db);
foreach ($tenantRows as $idx => $tenantRow) {
    $tenantRows[$idx]['domains'] = programmit_saas_list_domains_by_tenant($db, (int)$tenantRow['id']);
}

$owners = array();
$ownerQry = $db->sql_query("SELECT user_id, user_name, user_level
    FROM users
    WHERE user_level IN ('superadmin','administrator','subadmin','reseller','subreseller')
    ORDER BY user_id ASC
    LIMIT 500");
while ($ownerRow = $db->sql_fetchrow($ownerQry)) {
    if (!$ownerRow) {
        continue;
    }
    $owners[] = array(
        'user_id' => (int)$ownerRow['user_id'],
        'user_name' => (string)$ownerRow['user_name'],
        'user_level' => (string)$ownerRow['user_level']
    );
}

$smarty->assign('page', 'saas-tenants');
$smarty->assign('saas_tenant_error', $saas_tenant_error);
$smarty->assign('saas_tenant_success', $saas_tenant_success);
$smarty->assign('saas_edit_tenant', $editTenant);
$smarty->assign('saas_plan_rows', $planRows);
$smarty->assign('saas_tenant_rows', $tenantRows);
$smarty->assign('saas_owner_rows', $owners);
$smarty->display('saas-tenants.tpl');
?>
