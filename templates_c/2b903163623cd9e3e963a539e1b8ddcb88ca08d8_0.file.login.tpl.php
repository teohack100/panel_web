<?php
/* Smarty version 3.1.29, created on 2026-03-11 19:39:51
  from "C:\xampp\htdocs\panel_web\templates\login.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_69b21967a7e2b8_85172937',
  'file_dependency' => 
  array (
    '2b903163623cd9e3e963a539e1b8ddcb88ca08d8' => 
    array (
      0 => 'C:\\xampp\\htdocs\\panel_web\\templates\\login.tpl',
      1 => 1772758728,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b21967a7e2b8_85172937 ($_smarty_tpl) {
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title><?php echo $_smarty_tpl->tpl_vars['siteTitle']->value;?>
 - Iniciar sesion</title>
    <?php $_smarty_tpl->tpl_vars['pm_login_favicon'] = new Smarty_Variable(((string)$_smarty_tpl->tpl_vars['base_url']->value)."logo/favicon2.png?v=2", null);
$_smarty_tpl->ext->_updateScope->updateScope($_smarty_tpl, 'pm_login_favicon', 0);?>
    <?php if (isset($_smarty_tpl->tpl_vars['panel_favicon_url']->value) && $_smarty_tpl->tpl_vars['panel_favicon_url']->value != '') {?>
        <?php $_smarty_tpl->tpl_vars['pm_login_favicon'] = new Smarty_Variable($_smarty_tpl->tpl_vars['panel_favicon_url']->value, null);
$_smarty_tpl->ext->_updateScope->updateScope($_smarty_tpl, 'pm_login_favicon', 0);?>
    <?php }?>
    <link rel="icon" type="image/png" href="<?php echo $_smarty_tpl->tpl_vars['pm_login_favicon']->value;?>
">
    <link rel="shortcut icon" type="image/png" href="<?php echo $_smarty_tpl->tpl_vars['pm_login_favicon']->value;?>
">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700;800&family=Nunito+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --bg0: #173154;
            --bg1: #262f73;
            --panel: #234a73;
            --panel-dark: #2b2e69;
            --input: rgba(10, 28, 58, 0.62);
            --line: rgba(109, 156, 235, 0.34);
            --text: #ecf3ff;
            --muted: #bed1ef;
            --yellow: #2b9dff;
            --btn-blue: #5978ff;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Nunito Sans', sans-serif;
            color: var(--text);
            background:
                radial-gradient(980px 520px at -20% -10%, rgba(75, 152, 255, 0.2), transparent 62%),
                radial-gradient(900px 560px at 120% 110%, rgba(111, 67, 255, 0.18), transparent 60%),
                linear-gradient(150deg, var(--bg0) 0%, var(--bg1) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }

        .auth-card {
            width: 100%;
            max-width: 430px;
            background: linear-gradient(180deg, var(--panel) 0%, var(--panel-dark) 100%);
            border: 1px solid var(--line);
            border-radius: 8px;
            box-shadow: 0 24px 56px rgba(6, 14, 35, 0.45);
            padding: 30px 28px;
        }

        .auth-title {
            margin: 0;
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            font-size: 2rem;
            letter-spacing: 0.3px;
        }
        .login-logo-wrap {
            text-align: center;
            margin-bottom: 14px;
        }
        .login-logo-wrap img {
            max-width: 180px;
            max-height: 58px;
            width: auto;
            height: auto;
            object-fit: contain;
            filter: drop-shadow(0 4px 10px rgba(3, 12, 30, .4));
        }

        .auth-sub {
            margin: 6px 0 20px;
            font-size: 1rem;
            color: var(--muted);
            font-weight: 700;
        }

        .auth-sub a {
            color: #5cc3ff;
            text-decoration: none;
            font-weight: 800;
        }

        .pm-alert,
        .alert {
            border-radius: 6px;
            padding: 10px 12px;
            margin-bottom: 14px;
            border: 1px solid transparent;
            font-weight: 700;
            font-size: 0.92rem;
        }

        .pm-alert-success,
        .alert-success {
            background: rgba(30, 202, 184, 0.18);
            color: #d8fff7;
            border-color: rgba(30, 202, 184, 0.62);
        }

        .pm-alert-danger,
        .alert-danger {
            background: rgba(255, 117, 137, 0.18);
            color: #ffe5eb;
            border-color: rgba(255, 99, 132, 0.62);
        }

        .pm-alert-info,
        .alert-info,
        .alert-warning {
            background: rgba(77, 121, 246, 0.18);
            color: #eaf1ff;
            border-color: rgba(77, 121, 246, 0.45);
        }

        .social-btn,
        .btn-main,
        .btn-alt {
            width: 100%;
            border: 0;
            border-radius: 0;
            padding: 13px 14px;
            font-weight: 800;
            font-size: 1.06rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            transition: transform .14s ease, opacity .14s ease;
            text-decoration: none;
        }

        .social-btn:hover,
        .btn-main:hover,
        .btn-alt:hover {
            transform: translateY(-1px);
        }

        .social-stack {
            display: grid;
            gap: 9px;
            margin-bottom: 10px;
        }

        .social-google {
            background: linear-gradient(180deg, #ef4444 0%, #dc2626 100%);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .social-facebook { background: var(--btn-blue); color: #fff; }
        .social-apple { background: #050505; color: #fff; }

        .google-g-icon {
            width: 20px;
            height: 20px;
            display: inline-block;
            flex: 0 0 20px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 533.5 544.3'%3E%3Cpath fill='%234285f4' d='M533.5 278.4c0-17.4-1.5-34.1-4.3-50.2H272v95h146.9c-6.4 34.4-25.8 63.6-55 83.1v68h88.8c52-47.8 80.8-118.3 80.8-195.9z'/%3E%3Cpath fill='%2334a853' d='M272 544.3c73.5 0 135.2-24.3 180.2-65.8l-88.8-68c-24.6 16.5-56.1 26.2-91.4 26.2-70.3 0-129.8-47.5-151.1-111.3H28.7v69.9c44.8 88.8 136.8 149 243.3 149z'/%3E%3Cpath fill='%23fbbc04' d='M120.9 325.4c-10.9-32.5-10.9-67.7 0-100.2v-69.9H28.7c-39.8 79.2-39.8 171 0 250.2l92.2-70.1z'/%3E%3Cpath fill='%23ea4335' d='M272 107.7c37.9-.6 74.4 13.6 102.2 39.7l76.4-76.4C406.9 24.6 345.2-.7 272 0 165.5 0 73.5 60.2 28.7 149l92.2 69.9C142.2 155.1 201.7 107.7 272 107.7z'/%3E%3C/svg%3E");
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 14px 0;
            color: #d0e0ff;
            font-weight: 800;
            text-transform: lowercase;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--line);
        }

        .field-label {
            display: block;
            font-weight: 700;
            margin-bottom: 6px;
            color: #eaf2ff;
        }

        .field-input {
            width: 100%;
            border: 1px solid rgba(108, 145, 216, 0.4);
            border-radius: 0;
            background: var(--input);
            color: #edf4ff;
            padding: 14px 13px;
            font-size: 1.06rem;
            outline: none;
        }

        .field-input::placeholder { color: #9eb6dd; }

        .field-input:focus {
            border-color: #44b9ff;
            box-shadow: 0 0 0 2px rgba(68, 185, 255, 0.24);
        }

        .password-row {
            position: relative;
            margin-top: 8px;
        }

        .pass-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: 0;
            color: #c9dcff;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-main {
            margin-top: 14px;
            background: linear-gradient(14deg, #338dff 36%, #56c2ff 100%);
            color: #ffffff;
            border-radius: 0;
            box-shadow: 0 7px 20px rgba(51, 141, 255, 0.35);
        }

        .btn-main:disabled,
        .btn-alt:disabled,
        .social-btn:disabled {
            opacity: .7;
            cursor: not-allowed;
            transform: none;
        }

        .btn-alt {
            margin-top: 10px;
            background: rgba(7, 20, 45, 0.25);
            border: 1px solid rgba(68, 185, 255, 0.7);
            color: #82d5ff;
            border-radius: 0;
        }

        .hidden { display: none; }

        @media (max-width: 520px) {
            .auth-card {
                padding: 24px 18px;
            }

            .auth-title {
                font-size: 1.7rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <?php $_smarty_tpl->tpl_vars['pm_login_logo'] = new Smarty_Variable('', null);
$_smarty_tpl->ext->_updateScope->updateScope($_smarty_tpl, 'pm_login_logo', 0);?>
        <?php if (isset($_smarty_tpl->tpl_vars['panel_login_logo_url']->value) && $_smarty_tpl->tpl_vars['panel_login_logo_url']->value != '') {?>
            <?php $_smarty_tpl->tpl_vars['pm_login_logo'] = new Smarty_Variable($_smarty_tpl->tpl_vars['panel_login_logo_url']->value, null);
$_smarty_tpl->ext->_updateScope->updateScope($_smarty_tpl, 'pm_login_logo', 0);?>
        <?php } elseif (isset($_smarty_tpl->tpl_vars['panel_logo_url']->value) && $_smarty_tpl->tpl_vars['panel_logo_url']->value != '') {?>
            <?php $_smarty_tpl->tpl_vars['pm_login_logo'] = new Smarty_Variable($_smarty_tpl->tpl_vars['panel_logo_url']->value, null);
$_smarty_tpl->ext->_updateScope->updateScope($_smarty_tpl, 'pm_login_logo', 0);?>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['pm_login_logo']->value != '') {?>
        <div class="login-logo-wrap">
            <img src="<?php echo $_smarty_tpl->tpl_vars['pm_login_logo']->value;?>
" alt="Logo panel">
        </div>
        <?php }?>
        <h1 class="auth-title">Iniciar sesion</h1>
        <?php if ($_smarty_tpl->tpl_vars['control_allow_register']->value == 1) {?>
        <p class="auth-sub">¿Nuevo usuario? <a href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=register">Crear una cuenta</a></p>
        <?php } else { ?>
        <p class="auth-sub">Acceso solo para cuentas autorizadas</p>
        <?php }?>

        <?php if ($_smarty_tpl->tpl_vars['magic_notice_text']->value != '') {?>
        <div class="pm-alert pm-alert-<?php echo $_smarty_tpl->tpl_vars['magic_notice_class']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['magic_notice_text']->value;?>
</div>
        <?php }?>

        <div id="auth-message"></div>

        <?php if ($_smarty_tpl->tpl_vars['social_host_blocked']->value != 1) {?>
        <div class="social-stack">
            <a class="social-btn social-google" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=social-start&provider=google"><span class="google-g-icon" aria-hidden="true"></span> Continuar con Google</a>
            <a class="social-btn social-facebook" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=social-start&provider=facebook"><i class="fa-brands fa-facebook-f"></i> Continuar con Facebook</a>
            <a class="social-btn social-apple" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=social-start&provider=apple"><i class="fa-brands fa-apple"></i> Continuar con Apple</a>
        </div>

        <div class="divider">o</div>
        <?php }?>

        <form id="login-form" method="post" accept-charset="UTF-8" novalidate>
            <input type="hidden" id="submitted" name="submitted" value="Login Account">
            <input type="hidden" id="code" name="code" value="<?php echo $_smarty_tpl->tpl_vars['code']->value;?>
">
            <input type="hidden" id="category" name="category" value="<?php echo $_smarty_tpl->tpl_vars['login_encrypt']->value;?>
">

            <label class="field-label" for="user_name">Email o usuario</label>
            <input class="field-input" type="text" id="user_name" name="user_name" placeholder="Email o usuario" autocomplete="username" required>

            <div class="password-row">
                <input class="field-input" type="password" id="user_pass" name="user_pass" placeholder="Contrasena" autocomplete="current-password" required>
                <button type="button" id="toggle-password-eye" class="pass-toggle" aria-label="Mostrar contrasena"><i class="fa-regular fa-eye"></i></button>
            </div>

            <button type="submit" class="btn-main">Entrar al panel</button>
            <?php if ($_smarty_tpl->tpl_vars['control_allow_magic']->value == 1) {?>
            <button type="button" id="magic-link-btn" class="btn-alt">Obtener enlace magico</button>
            <?php }?>
        </form>
    </div>

    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/js/jquery.min.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
bootstrap/assets/jqueryform/jquery.form.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
>
        (function(){
            var $msg = $('#auth-message');
            var $form = $('#login-form');
            var $passInput = $('#user_pass');
            var $eye = $('#toggle-password-eye');
            var postLoginRedirect = '<?php echo (($tmp = @$_smarty_tpl->tpl_vars['post_login_redirect']->value)===null||$tmp==='' ? ((string)$_smarty_tpl->tpl_vars['base_url']->value)."index.php?p=dashboard" : $tmp);?>
';

            function forceDashboardIfSuccess(html){
                var text = String(html || '').toLowerCase();
                if(text.indexOf('location.reload') !== -1 || text.indexOf('iniciar sesion correctamente') !== -1){
                    window.location.href = postLoginRedirect;
                    return true;
                }
                return false;
            }

            function renderResponse(html){
                var holder = $('<div>').html(html || '');
                var scripts = holder.find('script');
                scripts.each(function(){
                    var scriptCode = this.text || this.textContent || this.innerHTML || '';
                    if(scriptCode){
                        try{
                            $.globalEval(scriptCode);
                        }catch(e){
                            // fallback redirect will run below
                        }
                    }
                });
                scripts.remove();
                $msg.html(holder.html());
                forceDashboardIfSuccess(html);
            }

            $eye.on('click', function(){
                var isPwd = $passInput.attr('type') === 'password';
                $passInput.attr('type', isPwd ? 'text' : 'password');
                $eye.html(isPwd ? '<i class="fa-regular fa-eye-slash"></i>' : '<i class="fa-regular fa-eye"></i>');
            });

            $('#magic-link-btn').on('click', function(){
                var emailOrUser = $.trim($('#user_name').val());
                if(emailOrUser === ''){
                    renderResponse('<div class="alert alert-danger"><strong>Ingresa tu email para generar el enlace magico.</strong></div>');
                    return;
                }

                var $btn = $(this);
                $btn.prop('disabled', true).text('Generando...');

                $.ajax({
                    type: 'POST',
                    url: '<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
serverside/forms/magic_link.php',
                    data: { user_email: emailOrUser, submitted: 'Magic Link' },
                    success: function(resp){ renderResponse(resp); },
                    error: function(xhr){
                        renderResponse(xhr.responseText || '<div class="alert alert-danger"><strong>No se pudo generar el enlace magico.</strong></div>');
                    },
                    complete: function(){ $btn.prop('disabled', false).text('Obtener enlace magico'); }
                });
            });

            $form.on('submit', function(e){
                e.preventDefault();

                if(typeof $form.ajaxSubmit === 'function'){
                    $form.ajaxSubmit({
                        type: 'POST',
                        url: '<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
serverside/forms/login.php',
                        success: function(resp){ renderResponse(resp); },
                        error: function(xhr){
                            renderResponse(xhr.responseText || '<div class="alert alert-danger"><strong>Error al iniciar sesion.</strong></div>');
                        }
                    });
                }else{
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
serverside/forms/login.php',
                        data: $form.serialize(),
                        success: function(resp){ renderResponse(resp); },
                        error: function(xhr){
                            renderResponse(xhr.responseText || '<div class="alert alert-danger"><strong>Error al iniciar sesion.</strong></div>');
                        }
                    });
                }

                return false;
            });
        })();
    <?php echo '</script'; ?>
>
</body>
</html>
<?php }
}
