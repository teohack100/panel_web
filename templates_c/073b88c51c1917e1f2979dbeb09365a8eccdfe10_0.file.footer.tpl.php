<?php
/* Smarty version 3.1.29, created on 2026-03-11 19:13:43
  from "C:\xampp\htdocs\panel_web\templates\apps\footer.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_69b21347c73ad4_41190104',
  'file_dependency' => 
  array (
    '073b88c51c1917e1f2979dbeb09365a8eccdfe10' => 
    array (
      0 => 'C:\\xampp\\htdocs\\panel_web\\templates\\apps\\footer.tpl',
      1 => 1773277696,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b21347c73ad4_41190104 ($_smarty_tpl) {
?>
<footer class="footer text-center text-sm-left">
    &copy; 2020 <?php echo $_smarty_tpl->tpl_vars['siteTitle']->value;?>
 <span class="text-muted d-none d-sm-inline-block float-right">Elaborado con <i class="mdi mdi-heart text-danger"></i> Administrador PROGRAMMIT</span>
</footer>
<?php echo '<script'; ?>
>
if (typeof window.programmitHandleFormSaveResponse !== 'function') {
    window.programmitHandleFormSaveResponse = function(rawHtml, options) {
        var raw = (typeof rawHtml === 'string') ? rawHtml : String(rawHtml || '');
        var clean = raw.replace(/<?php echo '<script'; ?>
[\s\S]*?<\/script>/gi, ' ');
        var parser = document.createElement('div');
        parser.innerHTML = clean;
        var isFullPage = /<!doctype|<html[\s>]|<head[\s>]|<body[\s>]/i.test(raw);
        var alertMessages = [];
        var preferredAlertType = '';

        var fragment = '';
        if (parser.querySelectorAll) {
            var alertNodes = parser.querySelectorAll('.alert-success, .alert-danger');
            if (alertNodes && alertNodes.length) {
                for (var i = 0; i < alertNodes.length; i++) {
                    var node = alertNodes[i];
                    fragment += node.outerHTML;
                    var clone = node.cloneNode(true);
                    var closeButton = clone.querySelector ? clone.querySelector('.close') : null;
                    if (closeButton && closeButton.parentNode) {
                        closeButton.parentNode.removeChild(closeButton);
                    }
                    var alertText = '';
                    if (clone.textContent) {
                        alertText = clone.textContent;
                    } else if (clone.innerText) {
                        alertText = clone.innerText;
                    }
                    alertText = String(alertText || '').replace(/\s+/g, ' ').trim();
                    if (alertText !== '') {
                        alertMessages.push(alertText);
                    }
                    if (preferredAlertType === '' && node.className.indexOf('alert-danger') !== -1) {
                        preferredAlertType = 'error';
                    } else if (preferredAlertType === '' && node.className.indexOf('alert-success') !== -1) {
                        preferredAlertType = 'success';
                    }
                }
            }
        }

        var message = '';
        if (alertMessages.length) {
            message = alertMessages.join(' ');
        }
        if (parser.textContent) {
            message = message || parser.textContent;
        } else if (parser.innerText) {
            message = message || parser.innerText;
        }
        message = String(message || '').replace(/\s+/g, ' ').trim();

        if (message === '') {
            var htmlMatch = raw.match(/html:\s*['"]([^'"]+)['"]/i);
            if (htmlMatch && htmlMatch[1]) {
                message = String(htmlMatch[1]).trim();
            }
        }

        var isSuccess = raw.indexOf('alert-success') !== -1;
        var isError = raw.indexOf('alert-danger') !== -1;
        var isCleanSuccess = isSuccess && !isError && !isFullPage;
        var title = isCleanSuccess ? 'Registro completado' : 'No se pudo guardar';
        if (options && typeof options === 'object') {
            if (isCleanSuccess && options.successTitle) {
                title = options.successTitle;
            }
            if (!isCleanSuccess && options.errorTitle) {
                title = options.errorTitle;
            }
        }

        if (!isFullPage && preferredAlertType === 'success' && message !== '') {
            title = isCleanSuccess ? title : title;
        }

        if (fragment === '' && !isFullPage && (isSuccess || isError)) {
            fragment = clean;
        }

        if (isFullPage) {
            fragment = '';
        }

        if (isFullPage && !isSuccess && !isError) {
            message = 'El servidor devolvio una pagina completa en lugar de una respuesta del formulario.';
        }
        if (message === '') {
            message = isCleanSuccess ? 'La operacion se completo correctamente.' : 'Ocurrio un error al guardar los datos.';
        }

        if (typeof window.swal === 'function') {
            window.swal({
                type: isCleanSuccess ? 'success' : 'error',
                title: title,
                html: message,
                showConfirmButton: true,
                customClass: 'animated bounceIn swal2-popup',
                animation: false
            });
        } else if (window.alertify) {
            if (isCleanSuccess) {
                window.alertify.success(message);
            } else {
                window.alertify.error(message);
            }
        }

        return {
            ok: isCleanSuccess,
            hasError: isError || !isCleanSuccess,
            message: message,
            fragment: fragment,
            isFullPage: isFullPage,
            raw: raw
        };
    };
}

if (typeof window.programmitEscapeHtml !== 'function') {
    window.programmitEscapeHtml = function(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    };
}

if (typeof window.programmitNormalizeAjaxUrl !== 'function') {
    window.programmitNormalizeAjaxUrl = function(rawUrl) {
        if (typeof rawUrl !== 'string' || rawUrl === '') {
            return rawUrl;
        }

        if (!/^https?:\/\//i.test(rawUrl)) {
            return rawUrl;
        }

        try {
            var current = new URL(window.location.href);
            var target = new URL(rawUrl, current.href);
            var normalizedPath = (target.pathname || '/').replace(/\/+/g, '/');
            if (target.origin === current.origin) {
                return current.origin + normalizedPath + target.search + target.hash;
            }

            var currentPath = current.pathname || '/';
            var currentDir = currentPath.replace(/\/[^\/]*$/, '/');
            if (currentPath.indexOf('/index.php') !== -1) {
                currentDir = currentPath.split('/index.php')[0] + '/';
            }

            if (normalizedPath.indexOf(currentDir) === 0) {
                return current.origin + normalizedPath + target.search + target.hash;
            }
        } catch (err) {
            return rawUrl;
        }

        return rawUrl;
    };
}

if (typeof window.programmitApplyInlineFormResponse !== 'function') {
    window.programmitApplyInlineFormResponse = function($form, response) {
        if (!$form || !$form.length) {
            return;
        }

        var $summary = $form.find('.summary-errors');
        if (!$summary.length) {
            return;
        }

        var $list = $summary.find('ul');
        if (!$list.length) {
            $summary.html('<ul class="mb-0"></ul>');
            $list = $summary.find('ul');
        }

        if (response && response.ok) {
            $list.empty();
            $summary.hide();
            return;
        }

        var message = (response && response.message) ? response.message : 'Ocurrio un error al guardar los datos.';
        $list.html('<li>' + window.programmitEscapeHtml(message) + '</li>');
        $summary.show();
    };
}

if (typeof window.programmitSyncRegisterShadowFields !== 'function') {
    window.programmitSyncRegisterShadowFields = function() {
        var userNameEl = document.getElementById('user_name');
        var userPassEl = document.getElementById('user_pass');
        var fullNameEl = document.getElementById('register_full_name');
        var userEmailEl = document.getElementById('register_user_email');
        var userPass2El = document.getElementById('register_user_pass2');

        var userName = userNameEl ? String(userNameEl.value || '').trim() : '';
        var userPass = userPassEl ? String(userPassEl.value || '') : '';

        if (fullNameEl) {
            fullNameEl.value = userName;
        }
        if (userEmailEl) {
            userEmailEl.value = userName !== '' ? (userName + '@gmail.com') : '';
        }
        if (userPass2El) {
            userPass2El.value = userPass;
        }
    };
}

if (typeof window.jQuery !== 'undefined') {
    window.jQuery.ajaxPrefilter(function(options) {
        if (options && typeof options.url === 'string') {
            options.url = window.programmitNormalizeAjaxUrl(options.url);
        }
    });

    window.jQuery(document)
        .on('input change', '#register #user_name, #register #user_pass', function() {
            window.programmitSyncRegisterShadowFields();
        })
        .on('shown.bs.modal', '#modal_form', function() {
            window.programmitSyncRegisterShadowFields();
        });
}
<?php echo '</script'; ?>
>
<?php }
}
