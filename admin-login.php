<?php
define('DOC_ROOT_PATH', __DIR__ . '/');
require __DIR__ . '/includes/functions.php';
$user = isset($user) ? (string)$user : '';

function programmit_admin_role_allowed($userId, $userLevel) {
    if ((int)$userId === 1) {
        return true;
    }
    $role = strtolower(trim((string)$userLevel));
    return in_array($role, array('superadmin', 'administrator', 'subadmin'), true);
}

function programmit_admin_secure_set_cookie_local($name, $value, $expire, $path = '/') {
    if (function_exists('programmit_secure_set_cookie')) {
        programmit_secure_set_cookie((string)$name, (string)$value, (int)$expire, (string)$path);
        return;
    }
    setcookie((string)$name, (string)$value, (int)$expire, (string)$path);
}

function programmit_admin_make_auth_cookie($db, $userId, $userName, $userPass) {
    return $db->encrypt_key((int)$userId . '|' . (string)$userName . '|' . (string)$userPass);
}

function programmit_admin_auth_cookie_valid($db, $userId, $userName, $userPass) {
    if (!isset($_COOKIE['panel_admin_auth']) || trim((string)$_COOKIE['panel_admin_auth']) === '') {
        return false;
    }
    $raw = $db->decrypt_key((string)$_COOKIE['panel_admin_auth']);
    if (!is_string($raw) || $raw === '') {
        return false;
    }
    $parts = explode('|', $raw);
    if (!isset($parts[0], $parts[1], $parts[2])) {
        return false;
    }
    return (
        (int)$parts[0] === (int)$userId &&
        hash_equals((string)$parts[1], (string)$userName) &&
        hash_equals((string)$parts[2], (string)$userPass)
    );
}

$baseUrl = $db->base_url();
$error = '';
$usernameInput = '';

$errorCode = isset($_GET['error']) ? trim((string)$_GET['error']) : '';
if ($errorCode === 'role') {
    $error = 'Debes iniciar con una cuenta administrativa valida.';
}
$controlCode = isset($_GET['control']) ? trim((string)$_GET['control']) : '';
if ($controlCode === 'ip_blocked') {
    $error = 'IP no autorizada para acceso administrativo.';
} elseif ($controlCode === 'access_denied') {
    $error = 'Acceso denegado para esta cuenta en el host de control.';
}
if (isset($_GET['reauth']) && (string)$_GET['reauth'] === '1' && $error === '') {
    $error = 'Debes autenticarte en el login administrativo para entrar a admin.php.';
}

if (function_exists('programmit_control_is_host') && programmit_control_is_host($db)) {
    $controlIp = $db->get_client_ip();
    if (function_exists('programmit_control_security_ip_allowed') && !programmit_control_security_ip_allowed($db, $controlIp)) {
        $error = 'IP no autorizada para acceso administrativo.';
    }
}

if (isset($_GET['logout']) && (string)$_GET['logout'] === '1') {
    clear_auth_cookies();
    header('Location: ' . $baseUrl . 'admin-login.php');
    exit;
}

if (is_logged_in($user) && programmit_admin_role_allowed($user_id_2, $user_level_2) && programmit_admin_auth_cookie_valid($db, $user_id_2, $user_name_2, $auth_2)) {
    header('Location: ' . $baseUrl . 'admin.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameInput = trim((string)($_POST['user_name'] ?? ''));
    $passwordRaw = trim((string)($_POST['user_pass'] ?? ''));

    if ($error === '' && ($usernameInput === '' || $passwordRaw === '')) {
        $error = 'Usuario y contrasena son obligatorios.';
    }

    if ($error === '') {
        $passwordEncrypted = $db->encrypt_key($db->encryptor('encrypt', $passwordRaw));
        $qry = $db->sql_query(
            "SELECT user_id, user_name, user_pass, full_name, user_email, user_level, is_active, is_validated, is_ban, is_freeze, status, lastlogin
             FROM users
             WHERE user_name='" . $db->SanitizeForSQL($usernameInput) . "'
             AND user_pass='" . $db->SanitizeForSQL($passwordEncrypted) . "'
             LIMIT 1"
        );
        $row = $db->sql_fetchrow($qry);

        if (!$row) {
            $error = 'Credenciales invalidas.';
        } elseif (!programmit_admin_role_allowed((int)$row['user_id'], (string)$row['user_level'])) {
            $error = 'Esta cuenta no tiene permisos de administrador.';
        } elseif ((int)$row['is_validated'] !== 1 || (int)$row['is_active'] !== 1 || (int)$row['is_ban'] === 1 || (int)$row['is_freeze'] !== 0 || strtolower((string)$row['status']) !== 'live') {
            $error = 'La cuenta no esta habilitada para login administrativo.';
        } else {
            if (function_exists('programmit_control_is_host') && programmit_control_is_host($db)) {
                if (function_exists('programmit_control_security_user_allowed') && !programmit_control_security_user_allowed($db, $row)) {
                    $error = 'Acceso restringido para esta cuenta en host de control.';
                }
            }
        }

        if ($error === '' && $row) {
            $userId = (int)$row['user_id'];
            $userName = (string)$row['user_name'];
            $userPass = (string)$row['user_pass'];
            $fullName = (string)$row['full_name'];
            $userEmail = (string)$row['user_email'];
            $userLevel = (string)$row['user_level'];

            $lastLoginParts = explode(' ', (string)$row['lastlogin']);
            $lastDate = isset($lastLoginParts[0]) && trim((string)$lastLoginParts[0]) !== '' ? (string)$lastLoginParts[0] : date('Y-m-d');
            $lastTime = isset($lastLoginParts[1]) && trim((string)$lastLoginParts[1]) !== '' ? (string)$lastLoginParts[1] : date('H:i:s');
            $ipAddress = isset($_SERVER['REMOTE_ADDR']) ? (string)$_SERVER['REMOTE_ADDR'] : '';
            $exp = time() + 86400;

            $userCookie = $db->encrypt_key($userId . "|" . $userName . "|" . $userPass . "|" . $ipAddress . "|" . $lastDate . "|" . $lastTime . "|" . $userLevel);
            $adminCookie = programmit_admin_make_auth_cookie($db, $userId, $userName, $userPass);

            programmit_admin_secure_set_cookie_local('user', $userCookie, $exp, '/');
            programmit_admin_secure_set_cookie_local('user_id', $db->encrypt_key($userId), $exp, '/');
            programmit_admin_secure_set_cookie_local('full_name', $db->encrypt_key($fullName), $exp, '/');
            programmit_admin_secure_set_cookie_local('user_email', $db->encrypt_key($userEmail), $exp, '/');
            programmit_admin_secure_set_cookie_local('panel_admin_auth', $adminCookie, $exp, '/');

            $db->sql_query("UPDATE users
                SET ipaddress='" . $db->SanitizeForSQL($ipAddress) . "',
                    lastlogin=NOW(),
                    login_status='online',
                    last_active_time=NOW()
                WHERE user_id='" . $db->SanitizeForSQL($userId) . "'");

            header('Location: ' . $baseUrl . 'admin.php');
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login | Programmit Panel</title>
    <style>
        :root {
            --bg: #091120;
            --surface: #152844;
            --line: #2f4569;
            --txt: #eaf0fb;
            --muted: #9fb3d2;
            --danger: #ff6f88;
            --btn: #2f86ff;
            --btn2: #58b8ff;
            --accent: #6be0ff;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background:
                radial-gradient(920px 500px at -10% -10%, rgba(50, 142, 255, .26), transparent 62%),
                radial-gradient(880px 460px at 120% 120%, rgba(51, 223, 255, .14), transparent 60%),
                var(--bg);
            color: var(--txt);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            width: min(520px, 100%);
            border: 1px solid var(--line);
            border-radius: 16px;
            background:
                linear-gradient(180deg, rgba(26, 49, 84, .98) 0%, rgba(20, 36, 63, .98) 100%);
            box-shadow: 0 28px 56px rgba(2, 8, 20, .55);
            padding: 28px;
            position: relative;
            overflow: hidden;
        }
        .card::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(500px 120px at 20% 0%, rgba(107, 224, 255, .12), transparent 70%);
            pointer-events: none;
        }
        .head {
            position: relative;
            z-index: 1;
            margin-bottom: 8px;
        }
        .badge {
            display: inline-block;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .05em;
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid #3c5a86;
            color: #b9d8ff;
            margin-bottom: 10px;
        }
        h1 { margin: 0 0 6px; font-size: 30px; letter-spacing: .01em; }
        p { margin: 0 0 18px; color: var(--muted); line-height: 1.5; }
        .alert {
            margin-bottom: 12px;
            border: 1px solid rgba(255, 111, 136, .65);
            background: rgba(255, 111, 136, .12);
            color: #ffe4ea;
            border-radius: 10px;
            padding: 10px 12px;
            position: relative;
            z-index: 1;
        }
        label {
            display: block;
            font-size: 14px;
            margin: 10px 0 6px;
            color: #c4d4ec;
            position: relative;
            z-index: 1;
        }
        input {
            width: 100%;
            min-height: 46px;
            border-radius: 10px;
            border: 1px solid #3a5b8d;
            background: rgba(8, 22, 41, .74);
            color: #eef4ff;
            padding: 0 13px;
            font-size: 15px;
            outline: none;
            position: relative;
            z-index: 1;
        }
        input:focus {
            border-color: #67b7ff;
            box-shadow: 0 0 0 3px rgba(97, 175, 255, .22);
        }
        .actions {
            margin-top: 18px;
            display: flex;
            gap: 12px;
            position: relative;
            z-index: 1;
        }
        button, a.btn-link {
            min-height: 44px;
            border-radius: 10px;
            border: 1px solid #2f6ed2;
            background: linear-gradient(180deg, var(--btn2) 0%, var(--btn) 100%);
            color: #fff;
            padding: 0 18px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform .14s ease, opacity .14s ease;
            flex: 1;
        }
        button:hover, a.btn-link:hover {
            transform: translateY(-1px);
            opacity: .95;
        }
        a.btn-link {
            border-color: #3a5484;
            background: transparent;
            color: #d8e6fb;
        }
        .hint {
            margin-top: 12px;
            font-size: 12px;
            color: #95aed4;
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body>
    <main class="card">
        <div class="head">
            <span class="badge">Programmit Control</span>
            <h1>Admin Login</h1>
            <p>Acceso exclusivo para administradores de <strong>panel.programmit.com</strong>.</p>
        </div>
        <?php if ($error !== ''): ?>
            <div class="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <form method="post" action="<?php echo htmlspecialchars($baseUrl . 'admin-login.php', ENT_QUOTES, 'UTF-8'); ?>">
            <label for="user_name">Usuario administrador</label>
            <input type="text" id="user_name" name="user_name" autocomplete="username" required value="<?php echo htmlspecialchars($usernameInput, ENT_QUOTES, 'UTF-8'); ?>">

            <label for="user_pass">Contrasena</label>
            <input type="password" id="user_pass" name="user_pass" autocomplete="current-password" required>

            <div class="actions">
                <button type="submit">Entrar a admin</button>
                <a class="btn-link" href="<?php echo htmlspecialchars($baseUrl . 'index.php?p=login', ENT_QUOTES, 'UTF-8'); ?>">Login general</a>
            </div>
            <div class="hint">Seguridad activa: esta sesion admin es separada del login general.</div>
        </form>
    </main>
</body>
</html>
