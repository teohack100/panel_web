<?php

if (!function_exists('programmit_client_default_password_setting_key')) {
    function programmit_client_default_password_setting_key()
    {
        return 'client_default_password_enc';
    }
}

if (!function_exists('programmit_client_default_password_revision_setting_key')) {
    function programmit_client_default_password_revision_setting_key()
    {
        return 'client_default_password_revision';
    }
}

if (!function_exists('programmit_client_default_password_revision_at_setting_key')) {
    function programmit_client_default_password_revision_at_setting_key()
    {
        return 'client_default_password_revision_at';
    }
}

if (!function_exists('programmit_client_password_validation_error')) {
    function programmit_client_password_validation_error($plainPassword)
    {
        $plainPassword = (string)$plainPassword;
        if ($plainPassword === '') {
            return 'La contrasena esta vacia.';
        }
        if (preg_match('/[^_a-zA-Z0-9 !#$%&^~*.\-]/', $plainPassword)) {
            return 'Contrasena invalida.';
        }
        if (strlen($plainPassword) < 8) {
            return 'La contrasena debe tener al menos 8 caracteres.';
        }
        return '';
    }
}

if (!function_exists('programmit_client_default_password_encrypt')) {
    function programmit_client_default_password_encrypt($db, $plainPassword)
    {
        $plainPassword = trim((string)$plainPassword);
        if ($plainPassword === '') {
            return '';
        }
        return (string)$db->encrypt_key($db->encryptor('encrypt', $plainPassword));
    }
}

if (!function_exists('programmit_client_default_password_decrypt')) {
    function programmit_client_default_password_decrypt($db, $storedValue)
    {
        $storedValue = trim((string)$storedValue);
        if ($storedValue === '') {
            return '';
        }

        $decoded = (string)$db->decrypt_key($storedValue);
        if ($decoded === '') {
            return '';
        }

        $plain = (string)$db->encryptor('decrypt', $decoded);
        return trim((string)$plain);
    }
}

if (!function_exists('programmit_client_default_password_get')) {
    function programmit_client_default_password_get($db)
    {
        if (!function_exists('programmit_saas_get_setting')) {
            return '';
        }

        $storedValue = trim((string)programmit_saas_get_setting($db, programmit_client_default_password_setting_key(), ''));
        if ($storedValue === '') {
            return '';
        }

        return programmit_client_default_password_decrypt($db, $storedValue);
    }
}

if (!function_exists('programmit_client_default_password_is_configured')) {
    function programmit_client_default_password_is_configured($db)
    {
        return (programmit_client_default_password_get($db) !== '');
    }
}

if (!function_exists('programmit_client_default_password_revision_get')) {
    function programmit_client_default_password_revision_get($db)
    {
        if (!function_exists('programmit_saas_get_setting')) {
            return 0;
        }
        $revision = (int)programmit_saas_get_setting($db, programmit_client_default_password_revision_setting_key(), '0');
        if ($revision <= 0 && programmit_client_default_password_get($db) !== '') {
            return 1;
        }
        return $revision;
    }
}

if (!function_exists('programmit_client_default_password_revision_at_get')) {
    function programmit_client_default_password_revision_at_get($db)
    {
        if (!function_exists('programmit_saas_get_setting')) {
            return '';
        }
        return trim((string)programmit_saas_get_setting($db, programmit_client_default_password_revision_at_setting_key(), ''));
    }
}

if (!function_exists('programmit_client_default_password_touch_revision')) {
    function programmit_client_default_password_touch_revision($db)
    {
        if (!function_exists('programmit_saas_set_setting')) {
            return false;
        }

        $nextRevision = programmit_client_default_password_revision_get($db) + 1;
        $updatedAt = date('Y-m-d H:i:s');
        $okRevision = (bool)programmit_saas_set_setting(
            $db,
            programmit_client_default_password_revision_setting_key(),
            (string)$nextRevision
        );
        $okUpdatedAt = (bool)programmit_saas_set_setting(
            $db,
            programmit_client_default_password_revision_at_setting_key(),
            $updatedAt
        );
        return ($okRevision && $okUpdatedAt);
    }
}

if (!function_exists('programmit_client_default_password_set')) {
    function programmit_client_default_password_set($db, $plainPassword, &$errorMessage = '')
    {
        $errorMessage = '';
        $plainPassword = trim((string)$plainPassword);

        $validationError = programmit_client_password_validation_error($plainPassword);
        if ($validationError !== '') {
            $errorMessage = $validationError;
            return false;
        }

        if (!function_exists('programmit_saas_set_setting')) {
            $errorMessage = 'No se pudo guardar la configuracion.';
            return false;
        }

        $currentPassword = '';
        if (function_exists('programmit_client_default_password_get')) {
            $currentPassword = trim((string)programmit_client_default_password_get($db));
        }
        if ($currentPassword !== '' && hash_equals($currentPassword, $plainPassword)) {
            $errorMessage = 'La contrasena general actual ya esta en uso.';
            return false;
        }

        $saved = (bool)programmit_saas_set_setting(
            $db,
            programmit_client_default_password_setting_key(),
            programmit_client_default_password_encrypt($db, $plainPassword)
        );
        if ($saved) {
            programmit_client_default_password_touch_revision($db);
        }
        return $saved;
    }
}

if (!function_exists('programmit_client_default_password_clear')) {
    function programmit_client_default_password_clear($db)
    {
        if (!function_exists('programmit_saas_set_setting')) {
            return false;
        }

        $saved = (bool)programmit_saas_set_setting($db, programmit_client_default_password_setting_key(), '');
        if ($saved) {
            programmit_client_default_password_touch_revision($db);
        }
        return $saved;
    }
}

if (!function_exists('programmit_client_default_password_sync_meta')) {
    function programmit_client_default_password_sync_meta($db, $userRow = array())
    {
        $meta = array(
            'password_scope' => 'custom',
            'password_revision' => 0,
            'password_revision_at' => '',
        );

        if (!is_array($userRow)) {
            return $meta;
        }

        $userLevel = strtolower(trim((string)(isset($userRow['user_level']) ? $userRow['user_level'] : '')));
        if ($userLevel !== 'normal') {
            return $meta;
        }

        $defaultPassword = trim((string)programmit_client_default_password_get($db));
        if ($defaultPassword === '') {
            return $meta;
        }

        $rowPassPlain = trim((string)(isset($userRow['pass_plain']) ? $userRow['pass_plain'] : ''));
        $rowAuthVpn = trim((string)(isset($userRow['auth_vpn']) ? $userRow['auth_vpn'] : ''));
        $matchesDefault = ($rowPassPlain !== '' && hash_equals($rowPassPlain, $defaultPassword));
        if (!$matchesDefault && $rowAuthVpn !== '') {
            $matchesDefault = hash_equals($rowAuthVpn, md5($defaultPassword));
        }
        if (!$matchesDefault) {
            return $meta;
        }

        $meta['password_scope'] = 'global_default';
        $meta['password_revision'] = programmit_client_default_password_revision_get($db);
        $meta['password_revision_at'] = programmit_client_default_password_revision_at_get($db);
        return $meta;
    }
}

if (!function_exists('programmit_client_default_password_target_where')) {
    function programmit_client_default_password_target_where($db, $contextUserId = 0, $contextUserLevel = '')
    {
        $contextUserId = (int)$contextUserId;
        $contextUserLevel = strtolower(trim((string)$contextUserLevel));

        if ($contextUserId === 1 || $contextUserLevel === 'superadmin') {
            return "user_level='normal' AND user_id<>'" . $db->SanitizeForSQL($contextUserId) . "'";
        }

        if ($contextUserId > 0) {
            return "user_level='normal'"
                . " AND user_id<>'" . $db->SanitizeForSQL($contextUserId) . "'"
                . " AND upline='" . $db->SanitizeForSQL($contextUserId) . "'";
        }

        return "user_level='normal'";
    }
}

if (!function_exists('programmit_client_default_password_existing_summary')) {
    function programmit_client_default_password_existing_summary($db, $contextUserId = 0, $contextUserLevel = '')
    {
        $summary = array(
            'targeted' => 0,
            'needs_update' => 0,
        );

        $plainPassword = trim((string)programmit_client_default_password_get($db));
        if ($plainPassword === '') {
            return $summary;
        }

        $passwordEncrypted = trim((string)programmit_client_default_password_encrypt($db, $plainPassword));
        $authVpn = md5($plainPassword);
        $whereBase = programmit_client_default_password_target_where($db, $contextUserId, $contextUserLevel);
        $needsUpdateSql = "(COALESCE(pass_plain,'')<>'" . $db->SanitizeForSQL($plainPassword) . "'"
            . " OR COALESCE(auth_vpn,'')<>'" . $db->SanitizeForSQL($authVpn) . "'"
            . " OR COALESCE(user_pass,'')<>'" . $db->SanitizeForSQL($passwordEncrypted) . "')";

        $qry = $db->sql_query("SELECT COUNT(*) AS targeted,
                COALESCE(SUM(CASE WHEN " . $needsUpdateSql . " THEN 1 ELSE 0 END), 0) AS needs_update
            FROM users
            WHERE " . $whereBase);
        if ($qry) {
            $row = $db->sql_fetchrow($qry);
            if ($row && is_array($row)) {
                $summary['targeted'] = isset($row['targeted']) ? (int)$row['targeted'] : 0;
                $summary['needs_update'] = isset($row['needs_update']) ? (int)$row['needs_update'] : 0;
            }
        }

        return $summary;
    }
}

if (!function_exists('programmit_client_default_password_apply_to_existing')) {
    function programmit_client_default_password_apply_to_existing($db, &$result = array(), &$errorMessage = '', $contextUserId = 0, $contextUserLevel = '')
    {
        $result = array(
            'targeted' => 0,
            'changed' => 0,
            'reconcile' => array(),
        );
        $errorMessage = '';

        $plainPassword = trim((string)programmit_client_default_password_get($db));
        if ($plainPassword === '') {
            $errorMessage = 'Primero configura una contrasena general.';
            return false;
        }

        $validationError = programmit_client_password_validation_error($plainPassword);
        if ($validationError !== '') {
            $errorMessage = $validationError;
            return false;
        }

        $summary = programmit_client_default_password_existing_summary($db, $contextUserId, $contextUserLevel);
        $result['targeted'] = isset($summary['targeted']) ? (int)$summary['targeted'] : 0;
        $result['changed'] = isset($summary['needs_update']) ? (int)$summary['needs_update'] : 0;

        $passwordEncrypted = trim((string)programmit_client_default_password_encrypt($db, $plainPassword));
        $authVpn = md5($plainPassword);
        $whereBase = programmit_client_default_password_target_where($db, $contextUserId, $contextUserLevel);
        $needsUpdateSql = "(COALESCE(pass_plain,'')<>'" . $db->SanitizeForSQL($plainPassword) . "'"
            . " OR COALESCE(auth_vpn,'')<>'" . $db->SanitizeForSQL($authVpn) . "'"
            . " OR COALESCE(user_pass,'')<>'" . $db->SanitizeForSQL($passwordEncrypted) . "')";

        if (!$db->begin_transaction()) {
            $errorMessage = 'No se pudo iniciar la actualizacion de clientes.';
            return false;
        }

        $updateOk = $db->sql_query("UPDATE users
            SET user_pass='" . $db->SanitizeForSQL($passwordEncrypted) . "',
                pass_plain='" . $db->SanitizeForSQL($plainPassword) . "',
                auth_vpn='" . $db->SanitizeForSQL($authVpn) . "'
            WHERE " . $whereBase . "
              AND " . $needsUpdateSql);
        if (!$updateOk) {
            $db->rollback();
            $errorMessage = 'No se pudo aplicar la contrasena general a clientes existentes.';
            return false;
        }

        if (!$db->commit()) {
            $db->rollback();
            $errorMessage = 'No se pudo confirmar la actualizacion de clientes.';
            return false;
        }

        if (function_exists('programmit_vpn_reconcile_users')) {
            $result['reconcile'] = programmit_vpn_reconcile_users($db, true);
        }

        return true;
    }
}
