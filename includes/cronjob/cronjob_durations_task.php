<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once __DIR__ . '/config.php';

function resolve_state_file_path() {
    $sep = DIRECTORY_SEPARATOR;
    $candidates = array(
        __DIR__ . $sep . '.state',
        dirname(__DIR__) . $sep . 'tmp',
        rtrim((string)sys_get_temp_dir(), $sep) . $sep . 'programmit_panel'
    );

    foreach ($candidates as $dir) {
        if ($dir === '' || $dir === '.' || $dir === $sep) {
            continue;
        }
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        if (!is_dir($dir) || !is_writable($dir)) {
            continue;
        }
        $probe = $dir . $sep . '.probe_' . uniqid('', true);
        $ok = @file_put_contents($probe, 'ok', LOCK_EX);
        if ($ok !== false) {
            @unlink($probe);
            return $dir . $sep . 'durations_last_tick';
        }
    }
    return '';
}

$stateFile = resolve_state_file_path();
$persistState = ($stateFile !== '');

$now = time();
$last = 0;

if ($persistState && is_file($stateFile)) {
    $raw = trim((string)@file_get_contents($stateFile));
    if (is_numeric($raw)) {
        $last = (int)$raw;
    }
}

// Fallback robusto: si no hay estado persistente, descontar 60s por tick.
$elapsed = 60;

if ($last > 0 && $last <= $now) {
    $elapsed = $now - $last;
    if ($elapsed < 1) {
        $elapsed = 1;
    }
}

// Evita saltos extremos por reloj roto o cron parado demasiado tiempo.
if ($elapsed > 86400) {
    $elapsed = 86400;
}

if ($persistState) {
    @file_put_contents($stateFile, (string)$now, LOCK_EX);
}

$elapsed = (int)$elapsed;

$db->sql_query("UPDATE users SET role_duration = role_duration - {$elapsed} WHERE user_id!=1 AND is_freeze=0 AND role_duration > 0");
$db->sql_query("UPDATE users SET duration = duration - {$elapsed} WHERE user_id > 1 AND user_level!='superadmin' AND is_freeze=0 AND duration > 0");
$db->sql_query("UPDATE users SET vip_duration = vip_duration - {$elapsed} WHERE user_id > 1 AND user_level!='superadmin' AND is_freeze=0 AND vip_duration > 0");
$db->sql_query("UPDATE users SET private_duration = private_duration - {$elapsed} WHERE user_id > 1 AND user_level!='superadmin' AND is_freeze=0 AND private_duration > 0");
$db->sql_query("UPDATE users_delete SET delete_timestamp = delete_timestamp - {$elapsed} WHERE id > 0");

$db->sql_query("UPDATE users SET role_duration=0 WHERE user_id > 1 AND role_duration < 1");
$db->sql_query("UPDATE users SET duration=0 WHERE user_id > 1 AND user_level!='superadmin' AND is_freeze=0 AND duration < 1");
$db->sql_query("UPDATE users SET is_vip=0, vip_duration=0 WHERE user_id > 1 AND user_level!='superadmin' AND is_freeze=0 AND vip_duration < 1");
$db->sql_query("UPDATE users SET is_private=0, private_duration=0 WHERE user_id > 1 AND user_level!='superadmin' AND is_freeze=0 AND private_duration < 1");
$db->sql_query("UPDATE users_delete SET delete_timestamp=0 WHERE delete_timestamp < 1");

$db->sql_query("UPDATE users SET is_freeze=1, freeze_status=3, status='freeze' WHERE (user_level='reseller' OR user_level='admin' OR user_level='subadmin' OR user_level='subreseller' OR user_level='superadmin') AND (user_id!='1' OR user_level!='normal') AND role_duration=0");
$db->sql_query("UPDATE users SET is_passchange=0 WHERE is_passchange=1");

echo "Descuento aplicado. elapsed={$elapsed}s mode=" . ($persistState ? "elapsed" : "fixed60");
?>
