<footer class="footer text-center text-sm-left">
    &copy; 2020 {$siteTitle} <span class="text-muted d-none d-sm-inline-block float-right">Elaborado con <i class="mdi mdi-heart text-danger"></i> Administrador PROGRAMMIT</span>
</footer>
<script>
if (typeof window.programmitHandleFormSaveResponse !== 'function') {
    window.programmitHandleFormSaveResponse = function(rawHtml, options) {
        var raw = (typeof rawHtml === 'string') ? rawHtml : String(rawHtml || '');
        var clean = raw.replace(/<script[\s\S]*?<\/script>/gi, ' ');
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

if (typeof window.programmitResetRegisterValidation !== 'function') {
    window.programmitResetRegisterValidation = function() {
        if (typeof window.jQuery === 'undefined') {
            return;
        }
        var $form = window.jQuery('#register');
        if (!$form.length) {
            return;
        }
        $form.find('.summary-errors').hide().find('ul').empty();
        if ($form.data('formValidation')) {
            $form.formValidation('resetForm', true);
        }
    };
}

if (typeof window.programmitPrepareRegisterSubmit !== 'function') {
    window.programmitPrepareRegisterSubmit = function() {
        window.programmitSyncRegisterShadowFields();

        if (typeof window.save_method === 'undefined' || !window.save_method) {
            window.save_method = 'add';
        }

        var modeEl = document.getElementById('register_mode');
        if (modeEl) {
            modeEl.value = String(window.save_method || 'add');
        }

        var submitButton = document.getElementById('submitRegister');
        if (submitButton) {
            submitButton.disabled = false;
        }

        if (typeof window.programmitApplyRegisterPasswordMode === 'function') {
            window.programmitApplyRegisterPasswordMode();
        }
    };
}

window.programmitPrepareRegisterAddUi = function(options) {
    if (typeof window.jQuery === 'undefined') {
        return;
    }

    var config = options || {};
    var $ = window.jQuery;
    var $modal = $('#modal_form');

    window.save_method = 'add';

    var modeEl = document.getElementById('register_mode');
    if (modeEl) {
        modeEl.value = 'add';
    }

    $('#hidden').addClass('hidden');
    $('#secret').prop('disabled', true).val('');
    $('#resellers').prop('disabled', true).val('');
    $('#usname').removeClass('d-none');
    $('#upline').addClass('d-none');
    $('#role_mgt').removeClass('d-none');
    $('#role_mgt2').addClass('d-none');

    var defaultRole = String(config.defaultRole || '1');
    if ($('#role_acct option[value="' + defaultRole + '"]').length === 0) {
        defaultRole = '1';
    }

    if ($('#role_acct').length) {
        $('#role_acct').prop('disabled', false).val(defaultRole);
    }
    if ($('#role').length) {
        $('#role').prop('disabled', false).val(defaultRole);
    }

    if ($('#submitRegister').length) {
        $('#submitRegister').prop('disabled', false);
    }
    if ($('#loader').length) {
        $('#loader').empty();
    }

    var title = String(config.title || 'Agregar cliente');
    var submitText = String(config.submitText || 'Agregar usuario');
    $('.modal-title').text(title);
    if ($('#butext').length) {
        $('#butext').text(submitText);
    } else if ($('#submitRegister').length) {
        $('#submitRegister').html(submitText);
    }

    window.programmitSyncRegisterShadowFields();
    window.programmitApplyRegisterPasswordMode('add');

    // A few screens still mutate the role/selects right after opening the modal.
    window.setTimeout(function() {
        window.programmitApplyRegisterPasswordMode('add');
    }, 0);
    window.setTimeout(function() {
        window.programmitApplyRegisterPasswordMode('add');
    }, 180);
};

window.programmitHasGeneralClientPassword = function() {
    var enabledEl = document.getElementById('client_default_password_enabled');
    return !!(enabledEl && String(enabledEl.value || '0') === '1');
};

window.programmitGetRegisterMode = function() {
    var modeEl = document.getElementById('register_mode');
    var mode = modeEl ? String(modeEl.value || '') : '';
    if (mode === '' && typeof window.save_method !== 'undefined') {
        mode = String(window.save_method || '');
    }
    if (mode === '') {
        mode = 'add';
    }
    return mode.toLowerCase();
};

window.programmitGetRegisterRoleValue = function() {
    var roleAcctEl = document.getElementById('role_acct');
    var roleEl = document.getElementById('role');
    var value = '';

    if (roleAcctEl && String(roleAcctEl.value || '').trim() !== '') {
        value = String(roleAcctEl.value || '').trim();
    }
    if (value === '' && roleEl && String(roleEl.value || '').trim() !== '') {
        value = String(roleEl.value || '').trim();
    }
    if (value === '') {
        value = '1';
    }

    return value;
};

window.programmitRegisterUsesGeneralPassword = function(mode) {
    var normalizedMode = String(mode || window.programmitGetRegisterMode()).toLowerCase();
    var roleValue = window.programmitGetRegisterRoleValue();
    return window.programmitHasGeneralClientPassword() && normalizedMode !== 'update' && roleValue === '1';
};

window.programmitApplyRegisterPasswordMode = function(mode) {
    if (typeof window.jQuery === 'undefined') {
        return;
    }

    var normalizedMode = String(mode || window.programmitGetRegisterMode()).toLowerCase();
    var useGeneralPassword = window.programmitRegisterUsesGeneralPassword(normalizedMode);
    var $passwordGroup = window.jQuery('#register_password_group');
    var $passwordNote = window.jQuery('#register_password_note');
    var $passwordInput = window.jQuery('#user_pass');

    if ($passwordGroup.length) {
        $passwordGroup.toggle(!useGeneralPassword).toggleClass('d-none', useGeneralPassword);
    }
    if ($passwordNote.length) {
        $passwordNote.toggle(useGeneralPassword).toggleClass('d-none', !useGeneralPassword);
    }
    if ($passwordInput.length) {
        if (useGeneralPassword) {
            $passwordInput.val('');
            window.programmitUpdateRegisterPasswordMeter('');
        }
        $passwordInput.prop('required', !useGeneralPassword);
    }
};

if (typeof window.programmitRegisterPasswordScore !== 'function') {
    window.programmitRegisterPasswordScore = function(value) {
        var score = 0;
        value = String(value || '');
        if (value === '') {
            return null;
        }

        score += ((value.length >= 8) ? 1 : -1);
        if (/[A-Z]/.test(value)) {
            score += 1;
        }
        if (/[a-z]/.test(value)) {
            score += 1;
        }
        if (/[0-9]/.test(value)) {
            score += 1;
        }
        if (/[!#$%&^~*_]/.test(value)) {
            score += 1;
        }

        return score;
    };
}

if (typeof window.programmitUpdateRegisterPasswordMeter !== 'function') {
    window.programmitUpdateRegisterPasswordMeter = function(value) {
        var score = window.programmitRegisterPasswordScore(value);
        var $bar = window.jQuery ? window.jQuery('#signuppwdMeter').find('.progress-bar') : [];
        if (!$bar.length) {
            return;
        }

        switch (true) {
            case (score === null):
                $bar.html('').css('width', '0%').removeClass().addClass('progress-bar');
                break;
            case (score <= 0):
                $bar.html('Very weak').css('width', '25%').removeClass().addClass('progress-bar progress-bar-danger');
                break;
            case (score > 0 && score <= 2):
                $bar.html('Weak').css('width', '50%').removeClass().addClass('progress-bar progress-bar-warning');
                break;
            case (score > 2 && score <= 4):
                $bar.html('Medium').css('width', '75%').removeClass().addClass('progress-bar progress-bar-info');
                break;
            default:
                $bar.html('Strong').css('width', '100%').removeClass().addClass('progress-bar progress-bar-success');
                break;
        }
    };
}

window.programmitValidateRegisterForm = function() {
    var errors = [];
    var $ = window.jQuery;
    var userName = $ ? $.trim($('#user_name').val() || '') : '';
    var userPass = $ ? String($('#user_pass').val() || '') : '';
    var v2rayId = $ ? $.trim($('#v2ray_id').val() || '') : '';
    var registerMode = window.programmitGetRegisterMode();

    if (v2rayId === '') {
        errors.push('El UUID de V2Ray esta vacio.');
    }

    if (userName === '') {
        errors.push('El nombre de usuario esta vacio.');
    } else if (/[^_a-zA-Z0-9 -]/.test(userName)) {
        errors.push('Nombre de usuario invalido.');
    }

    if (!window.programmitRegisterUsesGeneralPassword(registerMode)) {
        if (userPass === '') {
            errors.push('La contrasena esta vacia.');
        } else if (/[^_a-zA-Z0-9 !#$%&^~*.-]/.test(userPass)) {
            errors.push('Contrasena invalida.');
        } else if (userPass.length < 8) {
            errors.push('La contrasena debe tener al menos 8 caracteres.');
        }
    }

    return errors;
};

if (typeof window.programmitRenderRegisterErrors !== 'function') {
    window.programmitRenderRegisterErrors = function(errors) {
        if (!window.jQuery) {
            return;
        }
        var $summary = window.jQuery('#register').find('.summary-errors');
        var $list = $summary.find('ul');
        if (!$list.length) {
            $summary.html('<ul class="mb-0"></ul>');
            $list = $summary.find('ul');
        }

        $list.empty();
        if (!errors || !errors.length) {
            $summary.hide();
            return;
        }

        for (var i = 0; i < errors.length; i++) {
            window.jQuery('<li/>').text(errors[i]).appendTo($list);
        }
        $summary.show();
    };
}

if (typeof window.programmitMoveModalToBody !== 'function') {
    window.programmitMoveModalToBody = function(selector) {
        if (typeof window.jQuery === 'undefined') {
            return;
        }
        var $modal = window.jQuery(selector);
        if (!$modal.length) {
            return;
        }
        if (!$modal.parent().is('body')) {
            $modal.appendTo(window.jQuery('body'));
        }
    };
}

if (typeof window.jQuery !== 'undefined') {
    window.jQuery(document).on('change', '#role_acct, #role', function () {
        if (typeof window.programmitApplyRegisterPasswordMode === 'function') {
            window.programmitApplyRegisterPasswordMode();
        }
    });
}

if (typeof window.programmitTranslateUiString !== 'function') {
    window.programmitTranslateUiString = function(value) {
        var text = String(value || '');
        var exactMap = {
            'Search..': 'Buscar...',
            'No matching records found': 'No se encontraron registros',
            'Copy': 'Copiar',
            'Print': 'Imprimir',
            'Save': 'Guardar',
            'Cancel': 'Cancelar',
            'Submit': 'Enviar',
            'Submit Ticket': 'Enviar ticket',
            'Add Credits': 'Agregar creditos',
            'Dashboard': 'Tablero',
            'My Profile': 'Mi perfil',
            'Auto recharge': 'Auto recarga',
            'History': 'Historial',
            'Updated': 'Actualizado',
            'Suspended Date': 'Fecha de suspension',
            'Suspended by': 'Suspendido por',
            "User's Suspended List": 'Lista de usuarios suspendidos',
            'Suspended / Unsuspended List': 'Lista de suspendidos / reactivados',
            'Freezed User List': 'Lista de usuarios congelados',
            'Last Freeze Date': 'Ultima fecha de congelado',
            'Duration History': 'Historial de duracion',
            'Add User Submit': 'Agregar usuario',
            'Freeze Account': 'Congelar cuenta',
            'Delete Account': 'Eliminar cuenta',
            'Self Durations:': 'Duraciones propias:',
            'Apply Self Reload!': 'Recargar para mi cuenta',
            'Your Credits': 'Tus creditos',
            'Convert Duration': 'Convertir duracion',
            'Convert Duration "Premium or VIP"': 'Convertir duracion "Premium o VIP"',
            'Duration Category:': 'Tipo de duracion:',
            'Quantity:': 'Cantidad:',
            'Port': 'Puerto',
            'Server Upload': 'Cargar servidor',
            'Choose file': 'Elegir archivo',
            'Submit Load Duration': 'Aplicar duracion',
            'Not enough credits': 'No tienes creditos suficientes.',
            'Successfully Unfreezed!': 'Descongelado correctamente.',
            'Successfully unfreezed and extended!': 'Descongelado y ampliado correctamente.',
            'Successfully Inactive Users Deleted!': 'Usuarios inactivos eliminados correctamente.',
            'Failed to Delete!': 'No se pudo eliminar.',
            'Please! Wait... While Generating Trial Account': 'Espera por favor... generando cuenta de prueba...',
            'Successfully Generated Trial Accounts!': 'Cuentas de prueba generadas correctamente.',
            'Successfully Suspended!...': 'Suspendido correctamente.',
            'Failed! to Delete!...': 'No se pudo eliminar.',
            'Successfully Freezed!...': 'Congelado correctamente.',
            'Failed! to Freezed!...': 'No se pudo congelar.',
            'Successfully Deleted!...': 'Eliminado correctamente.',
            'Successfully Unsuspended!...': 'Suspension retirada correctamente.',
            'Failed! Submitted Record': 'No se pudo guardar el registro.',
            'Successfully Submitted Record': 'Registro guardado correctamente.',
            'Declined': 'Cancelado',
            'Cancelled': 'Cancelado'
        };

        if (Object.prototype.hasOwnProperty.call(exactMap, text)) {
            return exactMap[text];
        }

        text = text.replace(/^Showing\s+(\d+)\s+to\s+(\d+)\s+of\s+(\d+)\s+entries$/i, 'Mostrando $1 a $2 de $3 registros');
        text = text.replace(/^Update User:\s*/i, 'Actualizar usuario: ');
        text = text.replace(/^Delete InActive\s*\(/i, 'Eliminar inactivos (');
        text = text.replace(/^Are you sure\?\s*Do you want to update\?$/i, 'Deseas actualizar?');
        text = text.replace(/^Are you sure\?\s*Do you want to delete\?$/i, 'Deseas eliminar?');
        text = text.replace(/^Are you sure\?\s*Do you want to freeze this selected user\?$/i, 'Deseas congelar este usuario seleccionado?');
        text = text.replace(/^Are you sure\?\s*Do you want to suspend the checked users\?$/i, 'Deseas suspender los usuarios seleccionados?');
        text = text.replace(/^Are you sure\?\s*Do you want to unsuspend the checked users\?$/i, 'Deseas reactivar los usuarios seleccionados?');
        text = text.replace(/^Are you sure\?\s*Do you want to delete all in-active user\?$/i, 'Deseas eliminar todos los usuarios inactivos?');
        text = text.replace(/^Are you sure\?\s*Do you want to Unfreezed this User\?$/i, 'Deseas descongelar este usuario?');
        text = text.replace(/^Are you sure\?\s*Do you want to Reload Credits for this user\?$/i, 'Deseas recargar creditos a este usuario?');
        text = text.replace(/^Are you sure\?\s*Do you want to Reload a Voucher for this user\?$/i, 'Deseas recargar un voucher a este usuario?');
        text = text.replace(/^Are you sure\?\s*Do you want to Reload Duration for this user\?$/i, 'Deseas ampliar la duracion de este usuario?');
        text = text.replace(/^Please!\s*Wait!\.\.\.\s*While Uploading Data\.\.\.$/i, 'Espera por favor... procesando datos...');

        return text;
    };
}

if (typeof window.programmitTranslateNodeText !== 'function') {
    window.programmitTranslateNodeText = function(root) {
        if (!root || !root.querySelectorAll) {
            return;
        }

        var nodes = root.querySelectorAll('button, a, label, th, td, h1, h2, h3, h4, h5, h6, small, p, span, li, option, div');
        for (var i = 0; i < nodes.length; i++) {
            var node = nodes[i];
            for (var j = 0; j < node.childNodes.length; j++) {
                var child = node.childNodes[j];
                if (!child || child.nodeType !== 3) {
                    continue;
                }

                var raw = child.nodeValue;
                if (!raw || raw.trim() === '') {
                    continue;
                }

                var trimmed = raw.trim();
                var translated = window.programmitTranslateUiString(trimmed);
                if (translated !== trimmed) {
                    child.nodeValue = raw.replace(trimmed, translated);
                }
            }
        }
    };
}

if (typeof window.programmitLocalizeFrontendAttributes !== 'function') {
    window.programmitLocalizeFrontendAttributes = function(root) {
        if (!root || !root.querySelectorAll) {
            return;
        }

        var attrs = ['placeholder', 'title', 'aria-label', 'data-original-title'];
        var nodes = root.querySelectorAll('[placeholder], [title], [aria-label], [data-original-title]');
        for (var i = 0; i < nodes.length; i++) {
            var node = nodes[i];
            for (var j = 0; j < attrs.length; j++) {
                var attr = attrs[j];
                var current = node.getAttribute(attr);
                if (!current) {
                    continue;
                }
                var translated = window.programmitTranslateUiString(current);
                if (translated !== current) {
                    node.setAttribute(attr, translated);
                }
            }
        }
    };
}

if (typeof window.programmitLocalizeDataTablesUi !== 'function') {
    window.programmitLocalizeDataTablesUi = function(root) {
        if (!root || !root.querySelectorAll) {
            return;
        }

        var wrappers = root.querySelectorAll('.dataTables_wrapper');
        for (var i = 0; i < wrappers.length; i++) {
            var wrapper = wrappers[i];

            var searchInput = wrapper.querySelector('.dataTables_filter input');
            if (searchInput) {
                searchInput.setAttribute('placeholder', 'Buscar...');
            }

            var info = wrapper.querySelector('.dataTables_info');
            if (info && info.textContent) {
                info.textContent = window.programmitTranslateUiString(info.textContent.trim());
            }

            var empty = wrapper.querySelector('td.dataTables_empty');
            if (empty && empty.textContent) {
                empty.textContent = window.programmitTranslateUiString(empty.textContent.trim());
            }

            var buttons = wrapper.querySelectorAll('.dt-button');
            for (var j = 0; j < buttons.length; j++) {
                var titleAttr = buttons[j].getAttribute('title') || buttons[j].getAttribute('titleAttr') || buttons[j].getAttribute('data-original-title');
                if (titleAttr) {
                    var translatedTitle = window.programmitTranslateUiString(titleAttr);
                    buttons[j].setAttribute('title', translatedTitle);
                    buttons[j].setAttribute('data-original-title', translatedTitle);
                }
            }
        }
    };
}

if (typeof window.programmitApplyFrontendLocalization !== 'function') {
    window.programmitApplyFrontendLocalization = function(root) {
        var scope = root || document;
        window.programmitTranslateNodeText(scope);
        window.programmitLocalizeFrontendAttributes(scope);
        window.programmitLocalizeDataTablesUi(scope);
    };
}

if (typeof window.programmitPatchAlertLibraries !== 'function') {
    window.programmitPatchAlertLibraries = function() {
        if (window.alertify && !window.alertify.programmitLocalized) {
            if (window.alertify.defaults && window.alertify.defaults.glossary) {
                window.alertify.defaults.glossary.ok = 'Aceptar';
                window.alertify.defaults.glossary.cancel = 'Cancelar';
            }

            ['success', 'error', 'message', 'warning', 'notify'].forEach(function(method) {
                if (typeof window.alertify[method] !== 'function') {
                    return;
                }
                var original = window.alertify[method];
                window.alertify[method] = function(message) {
                    if (typeof message === 'string') {
                        message = window.programmitTranslateUiString(message);
                    }
                    return original.apply(this, arguments.length ? [message].concat([].slice.call(arguments, 1)) : arguments);
                };
            });

            if (typeof window.alertify.confirm === 'function') {
                var originalConfirm = window.alertify.confirm;
                window.alertify.confirm = function(message) {
                    if (typeof message === 'string') {
                        arguments[0] = window.programmitTranslateUiString(message);
                    }
                    return originalConfirm.apply(this, arguments);
                };
            }

            window.alertify.programmitLocalized = true;
        }

        if (typeof window.swal === 'function' && !window.swal.programmitLocalized) {
            var originalSwal = window.swal;
            var wrappedSwal = function() {
                var args = Array.prototype.slice.call(arguments);
                if (args.length === 1 && args[0] && typeof args[0] === 'object') {
                    if (typeof args[0].title === 'string') {
                        args[0].title = window.programmitTranslateUiString(args[0].title);
                    }
                    if (typeof args[0].text === 'string') {
                        args[0].text = window.programmitTranslateUiString(args[0].text);
                    }
                    if (typeof args[0].html === 'string') {
                        args[0].html = window.programmitTranslateUiString(args[0].html);
                    }
                    if (typeof args[0].confirmButtonText === 'string') {
                        args[0].confirmButtonText = window.programmitTranslateUiString(args[0].confirmButtonText);
                    }
                    if (typeof args[0].cancelButtonText === 'string') {
                        args[0].cancelButtonText = window.programmitTranslateUiString(args[0].cancelButtonText);
                    }
                } else {
                    for (var i = 0; i < Math.min(args.length, 2); i++) {
                        if (typeof args[i] === 'string') {
                            args[i] = window.programmitTranslateUiString(args[i]);
                        }
                    }
                }
                return originalSwal.apply(this, args);
            };
            for (var key in originalSwal) {
                if (Object.prototype.hasOwnProperty.call(originalSwal, key)) {
                    wrappedSwal[key] = originalSwal[key];
                }
            }
            wrappedSwal.programmitLocalized = true;
            window.swal = wrappedSwal;
        }
    };
}

if (typeof window.jQuery !== 'undefined') {
    window.jQuery.ajaxPrefilter(function(options) {
        if (options && typeof options.url === 'string') {
            options.url = window.programmitNormalizeAjaxUrl(options.url);
        }
    });

    window.jQuery(function() {
        window.programmitPatchAlertLibraries();
        window.programmitMoveModalToBody('#modal_form');
        window.programmitMoveModalToBody('#instant_form');
        window.programmitApplyFrontendLocalization(document);
    });

    window.jQuery(document)
        .on('input change', '#register #user_name, #register #user_pass', function() {
            window.programmitSyncRegisterShadowFields();
        })
        .on('click', '#submitRegister', function() {
            window.programmitPrepareRegisterSubmit();
        })
        .on('submit', '#register', function() {
            window.programmitPrepareRegisterSubmit();
        })
        .on('shown.bs.modal', '#modal_form', function() {
            window.programmitSyncRegisterShadowFields();
            window.programmitApplyRegisterPasswordMode();
            window.programmitApplyFrontendLocalization(this);
            window.setTimeout(function() {
                window.programmitApplyRegisterPasswordMode();
            }, 120);
        })
        .on('hidden.bs.modal', '#modal_form', function() {
            window.programmitApplyRegisterPasswordMode('add');
        })
        .on('shown.bs.modal', '.modal', function() {
            window.programmitApplyFrontendLocalization(this);
        })
        .on('draw.dt', function(e) {
            window.programmitApplyFrontendLocalization(document);
        })
        .ajaxComplete(function() {
            window.programmitApplyFrontendLocalization(document);
        });
}
</script>
