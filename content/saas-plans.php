<?php
chkSession();

if (!programmit_saas_is_platform_admin($user_id_2, $user_level_2)) {
    header("Location: ".$db->base_url()."index.php?p=dashboard");
    exit;
}

programmit_saas_ensure_tables($db);
$controlHost = programmit_saas_get_control_host($db);
if (!programmit_saas_can_manage_from_current_host($db)) {
    header("Location: https://" . $controlHost . "/index.php?p=saas-plans");
    exit;
}

$saas_plan_error = '';
$saas_plan_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_saas_plan'])) {
    $planId = isset($_POST['plan_id']) ? (int)$_POST['plan_id'] : 0;
    $planCode = strtolower(trim((string)$_POST['plan_code']));
    $planName = trim((string)$_POST['plan_name']);
    $description = trim((string)$_POST['description']);
    $monthlyPrice = isset($_POST['monthly_price_usd']) ? (float)$_POST['monthly_price_usd'] : 0;
    $setupFee = isset($_POST['setup_fee_usd']) ? (float)$_POST['setup_fee_usd'] : 0;
    $creditPrice = isset($_POST['credit_price_usd']) ? (float)$_POST['credit_price_usd'] : 0;
    $includedCredits = isset($_POST['included_credits']) ? (int)$_POST['included_credits'] : 0;
    $panelLimit = isset($_POST['panel_limit']) ? (int)$_POST['panel_limit'] : 1;
    $userLimit = isset($_POST['user_limit']) ? (int)$_POST['user_limit'] : 1;
    $methodLimit = isset($_POST['method_limit']) ? (int)$_POST['method_limit'] : 1;
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    $isPublic = isset($_POST['is_public']) ? 1 : 0;

    if (!programmit_saas_valid_key($planCode)) {
        $saas_plan_error = 'Codigo de plan invalido (usa a-z, 0-9, guion o guion bajo).';
    } elseif ($planName === '') {
        $saas_plan_error = 'Nombre del plan obligatorio.';
    } elseif ($monthlyPrice < 0 || $setupFee < 0 || $creditPrice <= 0) {
        $saas_plan_error = 'Precios invalidos.';
    } elseif ($includedCredits < 0 || $panelLimit <= 0 || $userLimit <= 0 || $methodLimit <= 0) {
        $saas_plan_error = 'Limites invalidos.';
    } else {
        $dupWhere = ($planId > 0)
            ? " AND id<>'".$db->SanitizeForSQL($planId)."'"
            : "";
        $dupQry = $db->sql_query("SELECT id FROM saas_plans
            WHERE plan_code='".$db->SanitizeForSQL($planCode)."'
            ".$dupWhere."
            LIMIT 1");
        if ($dupQry && $db->sql_numrows($dupQry) > 0) {
            $saas_plan_error = 'Ya existe un plan con ese codigo.';
        } else {
            if ($planId > 0) {
                $ok = $db->sql_query("UPDATE saas_plans
                    SET plan_code='".$db->SanitizeForSQL($planCode)."',
                        plan_name='".$db->SanitizeForSQL($planName)."',
                        description='".$db->SanitizeForSQL($description)."',
                        monthly_price_usd='".$db->SanitizeForSQL(number_format($monthlyPrice, 2, '.', ''))."',
                        setup_fee_usd='".$db->SanitizeForSQL(number_format($setupFee, 2, '.', ''))."',
                        credit_price_usd='".$db->SanitizeForSQL(number_format($creditPrice, 4, '.', ''))."',
                        included_credits='".$db->SanitizeForSQL($includedCredits)."',
                        panel_limit='".$db->SanitizeForSQL($panelLimit)."',
                        user_limit='".$db->SanitizeForSQL($userLimit)."',
                        method_limit='".$db->SanitizeForSQL($methodLimit)."',
                        is_active='".$db->SanitizeForSQL($isActive)."',
                        is_public='".$db->SanitizeForSQL($isPublic)."',
                        updated_at=NOW()
                    WHERE id='".$db->SanitizeForSQL($planId)."'
                    LIMIT 1");
                if ($ok) {
                    $saas_plan_success = 'Plan actualizado correctamente.';
                } else {
                    $saas_plan_error = 'No se pudo actualizar el plan.';
                }
            } else {
                $ok = $db->sql_query("INSERT INTO saas_plans
                    (plan_code, plan_name, description, monthly_price_usd, setup_fee_usd, credit_price_usd,
                     included_credits, panel_limit, user_limit, method_limit, is_active, is_public, created_at, updated_at)
                    VALUES
                    ('".$db->SanitizeForSQL($planCode)."',
                     '".$db->SanitizeForSQL($planName)."',
                     '".$db->SanitizeForSQL($description)."',
                     '".$db->SanitizeForSQL(number_format($monthlyPrice, 2, '.', ''))."',
                     '".$db->SanitizeForSQL(number_format($setupFee, 2, '.', ''))."',
                     '".$db->SanitizeForSQL(number_format($creditPrice, 4, '.', ''))."',
                     '".$db->SanitizeForSQL($includedCredits)."',
                     '".$db->SanitizeForSQL($panelLimit)."',
                     '".$db->SanitizeForSQL($userLimit)."',
                     '".$db->SanitizeForSQL($methodLimit)."',
                     '".$db->SanitizeForSQL($isActive)."',
                     '".$db->SanitizeForSQL($isPublic)."',
                     NOW(),
                     NOW())");
                if ($ok) {
                    $saas_plan_success = 'Plan creado correctamente.';
                } else {
                    $saas_plan_error = 'No se pudo crear el plan.';
                }
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_saas_plan'])) {
    $deleteId = isset($_POST['delete_plan_id']) ? (int)$_POST['delete_plan_id'] : 0;
    if ($deleteId > 0) {
        $useQry = $db->sql_query("SELECT COUNT(*) AS total
            FROM saas_tenants
            WHERE plan_id='".$db->SanitizeForSQL($deleteId)."'");
        $useRow = $db->sql_fetchrow($useQry);
        $used = $useRow ? (int)$useRow['total'] : 0;

        if ($used > 0) {
            $db->sql_query("UPDATE saas_plans
                SET is_active='0', updated_at=NOW()
                WHERE id='".$db->SanitizeForSQL($deleteId)."'
                LIMIT 1");
            $saas_plan_success = 'Plan en uso: se desactivo en lugar de eliminarse.';
        } else {
            $db->sql_query("DELETE FROM saas_plans
                WHERE id='".$db->SanitizeForSQL($deleteId)."'
                LIMIT 1");
            $saas_plan_success = 'Plan eliminado.';
        }
    }
}

$editPlanId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editPlan = null;
if ($editPlanId > 0) {
    $editQry = $db->sql_query("SELECT
        id, plan_code, plan_name, description, monthly_price_usd, setup_fee_usd, credit_price_usd,
        included_credits, panel_limit, user_limit, method_limit, is_active, is_public
        FROM saas_plans
        WHERE id='".$db->SanitizeForSQL($editPlanId)."'
        LIMIT 1");
    $editPlan = $db->sql_fetchrow($editQry);
}

if (!$editPlan) {
    $editPlan = array(
        'id' => 0,
        'plan_code' => '',
        'plan_name' => '',
        'description' => '',
        'monthly_price_usd' => 0,
        'setup_fee_usd' => 0,
        'credit_price_usd' => 1,
        'included_credits' => 0,
        'panel_limit' => 1,
        'user_limit' => 1,
        'method_limit' => 1,
        'is_active' => 1,
        'is_public' => 1
    );
}

$planRows = programmit_saas_list_plans($db);

$smarty->assign('page', 'saas-plans');
$smarty->assign('saas_plan_error', $saas_plan_error);
$smarty->assign('saas_plan_success', $saas_plan_success);
$smarty->assign('saas_edit_plan', $editPlan);
$smarty->assign('saas_plan_rows', $planRows);
$smarty->display('saas-plans.tpl');
?>
