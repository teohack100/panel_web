<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once __DIR__ . '/../../includes/functions.php';
chkSession();

$where_clause = "(is_groupname='normal' OR is_groupname='free')";
if(!($user_level_2 == 'superadmin' || $user_id_2 == 1)){
    $where_clause .= " AND upline='".$db->SanitizeForSQL($user_id_2)."'";
}

$sql = "SELECT user_id 
        FROM users 
        WHERE duration < 1 
        AND vip_duration < 1 
        AND private_duration < 1 
        AND is_active = 1 
        AND status = 'live' 
        AND $where_clause";

$query = $db->sql_query($sql);
$data = $db->sql_numrows($query);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($data);
?>
