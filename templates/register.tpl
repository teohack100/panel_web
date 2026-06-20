<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>{$siteTitle} - Crear cuenta</title>
    <link rel="icon" type="image/png" href="{$base_url}logo/favicon2.png?v=2">
    <link rel="shortcut icon" type="image/png" href="{$base_url}logo/favicon2.png?v=2">
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
            --yellow: #5cc3ff;
            --btn-blue: #5978ff;
            --ok: #8fffd1;
            --bad: #ffd3f5;
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

        .auth-sub {
            margin: 6px 0 18px;
            font-size: 1rem;
            color: var(--muted);
            font-weight: 700;
        }

        .auth-sub a {
            color: var(--yellow);
            text-decoration: none;
            font-weight: 800;
        }

        .alert {
            border-radius: 6px;
            padding: 10px 12px;
            margin-bottom: 14px;
            border: 1px solid transparent;
            font-weight: 700;
            font-size: 0.92rem;
        }

        .alert-success {
            background: rgba(24, 186, 142, 0.18);
            color: #d4ffef;
            border-color: rgba(24, 186, 142, 0.6);
        }

        .alert-danger {
            background: rgba(255, 87, 126, 0.18);
            color: #ffe0e8;
            border-color: rgba(255, 99, 132, 0.62);
        }

        .field {
            margin-bottom: 10px;
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

        .toggle-pass {
            margin: 4px 0 10px;
            color: #82d5ff;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            user-select: none;
        }

        .rule-list {
            margin: 4px 0 10px;
            padding: 0;
            list-style: none;
            color: #eaf2ff;
        }

        .rule-item {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 6px 0;
            font-weight: 700;
        }

        .rule-dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            border: 1px solid rgba(189, 214, 255, 0.55);
            background: transparent;
            flex: 0 0 10px;
        }

        .rule-item.ok .rule-dot {
            background: var(--ok);
            border-color: var(--ok);
            box-shadow: 0 0 0 2px rgba(105, 255, 167, 0.22);
        }

        .rule-item.bad .rule-dot {
            background: var(--bad);
            border-color: var(--bad);
            box-shadow: 0 0 0 2px rgba(255, 211, 245, 0.2);
        }

        .check-row {
            margin: 10px 0 14px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 1rem;
            line-height: 1.28;
        }

        .check-row input {
            margin-top: 2px;
            width: 18px;
            height: 18px;
        }

        .btn-main,
        .btn-social {
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

        .btn-main:hover,
        .btn-social:hover { transform: translateY(-1px); }

        .btn-main {
            background: linear-gradient(14deg, #338dff 36%, #56c2ff 100%);
            color: #ffffff;
            box-shadow: 0 7px 20px rgba(51, 141, 255, 0.35);
        }

        .btn-main:disabled,
        .btn-social:disabled {
            opacity: .7;
            cursor: not-allowed;
            transform: none;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 16px 0;
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

        .social-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0;
            margin-bottom: 12px;
        }

        .btn-google {
            background: linear-gradient(180deg, #ef4444 0%, #dc2626 100%);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .btn-facebook { background: var(--btn-blue); color: #fff; }
        .btn-apple { background: #050505; color: #fff; }

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

        .legal-copy {
            color: #d5e5ff;
            font-size: .98rem;
            line-height: 1.35;
            font-weight: 700;
        }

        .legal-copy a {
            color: #78d7ff;
            text-decoration: none;
            font-weight: 800;
        }

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
        <h1 class="auth-title">Crea tu cuenta</h1>
        <p class="auth-sub">¿Ya tienes una cuenta? <a href="{$base_url}index.php?p=login">Inicia sesion</a></p>

        {if $register_notice_text ne ''}
        <div class="alert alert-{$register_notice_class}">{$register_notice_text}</div>
        {/if}

        <div id="register-message"></div>

        <form id="register-form" method="post" accept-charset="UTF-8" novalidate>
            <input type="hidden" name="submitted" value="Register Account">

            <div class="field">
                <input class="field-input" type="email" id="user_email" name="user_email" placeholder="Email" autocomplete="email" required>
            </div>

            <div class="field">
                <input class="field-input" type="password" id="user_pass" name="user_pass" placeholder="Contrasena" autocomplete="new-password" required>
            </div>

            <div class="field">
                <input class="field-input" type="password" id="user_pass2" name="user_pass2" placeholder="Repetir contrasena" autocomplete="new-password" required>
            </div>

            <label class="toggle-pass" for="show_password">
                <input type="checkbox" id="show_password">
                Mostrar contrasena
            </label>

            <ul class="rule-list">
                <li id="rule-length" class="rule-item bad"><span class="rule-dot"></span>Al menos 8 caracteres</li>
                <li id="rule-complex" class="rule-item bad"><span class="rule-dot"></span>Al menos un numero (0-9) o simbolo especial</li>
            </ul>

            <label class="check-row" for="marketing_optin">
                <input type="checkbox" id="marketing_optin" name="marketing_optin" value="1">
                <span>Quiero recibir ofertas personalizadas con los mejores descuentos en juegos</span>
            </label>

            <button type="submit" id="register-submit" class="btn-main">Crea tu cuenta</button>
        </form>

        {if $social_host_blocked neq 1}
        <div class="divider">o</div>

        <div class="social-row">
            <a class="btn-social btn-google" href="{$base_url}index.php?p=social-start&provider=google"><span class="google-g-icon" aria-hidden="true"></span> Google</a>
            <a class="btn-social btn-facebook" href="{$base_url}index.php?p=social-start&provider=facebook"><i class="fa-brands fa-facebook-f"></i> Facebook</a>
            <a class="btn-social btn-apple" href="{$base_url}index.php?p=social-start&provider=apple"><i class="fa-brands fa-apple"></i> Apple</a>
        </div>
        {/if}

        <div class="legal-copy">
            Al crear una cuenta, confirmo que tengo al menos 16 años de edad y que acepto los
            <a href="javascript:void(0)">Terminos y Condiciones</a> y el
            <a href="javascript:void(0)">Aviso de Privacidad</a>.
        </div>
    </div>

    <script src="{$base_url}firenet/assets/js/jquery.min.js"></script>
    <script>
        (function(){
            var $form = $('#register-form');
            var $msg = $('#register-message');
            var $pass1 = $('#user_pass');
            var $pass2 = $('#user_pass2');
            var $show = $('#show_password');
            var $submit = $('#register-submit');

            function setRule($el, ok){
                $el.toggleClass('ok', ok).toggleClass('bad', !ok);
            }

            function evaluatePassword(){
                var value = $pass1.val() || '';
                var hasLength = value.length >= 8;
                var hasComplex = /[0-9\W_]/.test(value);
                setRule($('#rule-length'), hasLength);
                setRule($('#rule-complex'), hasComplex);
                return hasLength && hasComplex;
            }

            function renderMessage(html){
                var holder = $('<div>').html(html || '');
                var scripts = holder.find('script');
                scripts.each(function(){
                    var scriptCode = this.text || this.textContent || this.innerHTML || '';
                    if(scriptCode){
                        $.globalEval(scriptCode);
                    }
                });
                scripts.remove();
                $msg.html(holder.html());
            }

            function localError(text){
                renderMessage('<div class="alert alert-danger"><strong>' + text + '</strong></div>');
            }

            $show.on('change', function(){
                var type = this.checked ? 'text' : 'password';
                $pass1.attr('type', type);
                $pass2.attr('type', type);
            });

            $pass1.on('input', evaluatePassword);
            evaluatePassword();

            $form.on('submit', function(e){
                e.preventDefault();

                var email = $.trim($('#user_email').val());
                var p1 = $pass1.val();
                var p2 = $pass2.val();

                if(email === ''){
                    localError('Ingresa un email.');
                    return false;
                }

                var passOk = evaluatePassword();
                if(!passOk){
                    localError('La contrasena no cumple los requisitos minimos.');
                    return false;
                }

                if(p1 !== p2){
                    localError('Las contrasenas no coinciden.');
                    return false;
                }

                $submit.prop('disabled', true).text('Creando cuenta...');

                $.ajax({
                    type: 'POST',
                    url: '{$base_url}serverside/forms/register_modern.php',
                    data: $form.serialize(),
                    success: function(resp){
                        renderMessage(resp);
                        if(String(resp).indexOf('alert-success') !== -1){
                            setTimeout(function(){
                                window.location.href = '{$base_url}index.php?p=login';
                            }, 1600);
                        }
                    },
                    error: function(xhr){
                        renderMessage(xhr.responseText || '<div class="alert alert-danger"><strong>No se pudo crear la cuenta.</strong></div>');
                    },
                    complete: function(){
                        $submit.prop('disabled', false).text('Crea tu cuenta');
                    }
                });

                return false;
            });
        })();
    </script>
</body>
</html>
