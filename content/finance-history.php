<?php
chkSession();

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'finance_history.php';

$historyView = pm_finance_history_build_view_model($db, (int)$user_id_2, $_GET);
foreach ($historyView as $assignKey => $assignValue) {
    $smarty->assign($assignKey, $assignValue);
}

$smarty->assign('page', 'finance-history');
$smarty->display('finance-history.tpl');
