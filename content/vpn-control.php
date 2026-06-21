<?php
chkSession();

$embed_raw = isset($_GET['embed']) ? strtolower(trim((string)$_GET['embed'])) : '';
$vpn_embed_admin = in_array($embed_raw, array('1', 'admin', 'yes'), true);
$vpn_embed_qs = $vpn_embed_admin ? '&embed=admin' : '';
$vpn_view_raw = isset($_GET['vpn_view']) ? strtolower(trim((string)$_GET['vpn_view'])) : '';
$vpn_admin_view = 'manage-servers';
if ($vpn_view_raw === 'create-server' || $vpn_view_raw === 'manage-servers') {
    $vpn_admin_view = $vpn_view_raw;
}
$vpn_view_qs = $vpn_embed_qs . '&vpn_view=' . urlencode($vpn_admin_view);

if (!programmit_vpn_can_manage($user_id_2, $user_level_2)) {
    header("Location: ".$db->base_url()."index.php?p=dashboard");
    exit;
}

programmit_vpn_ensure_tables($db);

if (function_exists('programmit_saas_can_manage_from_current_host') && function_exists('programmit_saas_get_control_host')) {
    if (!programmit_saas_can_manage_from_current_host($db)) {
        $controlHost = programmit_saas_get_control_host($db);
        if ($vpn_embed_admin) {
            header("Location: https://" . $controlHost . "/index.php?p=vpn-control" . $vpn_view_qs);
        } else {
            header("Location: https://" . $controlHost . "/admin.php#vpn-manage-main");
        }
        exit;
    }
}

if (!$vpn_embed_admin) {
    header("Location: ".$db->base_url()."admin.php#vpn-manage-main");
    exit;
}

function programmit_vpn_control_find_by_id($rows, $id) {
    $id = (int)$id;
    foreach ((array)$rows as $row) {
        if ($row && isset($row['id']) && (int)$row['id'] === $id) {
            return $row;
        }
    }
    return null;
}

function programmit_vpn_control_remote_probe_command() {
    return "echo 'PROGRAMMIT SSH OK'\n"
        . "whoami\n"
        . "hostname\n"
        . "uname -sr\n"
        . "if command -v python3 >/dev/null 2>&1; then python3 --version; else echo 'python3:missing'; fi\n"
        . "if command -v systemctl >/dev/null 2>&1; then systemctl --version | head -n 1; else echo 'systemctl:missing'; fi";
}

function programmit_vpn_control_remote_output($stdout, $stderr) {
    $chunks = array();
    $stdout = trim((string)$stdout);
    $stderr = trim((string)$stderr);
    if ($stdout !== '') {
        $chunks[] = $stdout;
    }
    if ($stderr !== '') {
        $chunks[] = $stderr;
    }
    return trim(implode("\n\n", $chunks));
}

function programmit_vpn_control_run_remote_action($db, $serverRow, $actionName, $remoteExecSupported, $remoteExecReason, &$remoteActionTitle, &$remoteActionOutput, &$remoteActionStatus, &$successMessage, &$errorMessage) {
    $remoteActionTitle = '';
    $remoteActionOutput = '';
    $remoteActionStatus = '';
    $successMessage = '';
    $errorMessage = '';

    if (!is_array($serverRow) || empty($serverRow)) {
        $errorMessage = 'No se encontró el servidor para la acción remota.';
        return false;
    }

    $actionName = strtolower(trim((string)$actionName));
    if (!in_array($actionName, array('test', 'install', 'activate'), true)) {
        $errorMessage = 'La acción remota solicitada no es válida.';
        return false;
    }

    $serverId = isset($serverRow['id']) ? (int)$serverRow['id'] : 0;
    $controlAccess = isset($serverRow['control_access']) && is_array($serverRow['control_access'])
        ? $serverRow['control_access']
        : programmit_vpn_server_control_access($db, $serverRow);
    $targetHost = programmit_vpn_server_target_host($serverRow);
    $targetPort = isset($controlAccess['ssh_port']) ? (int)$controlAccess['ssh_port'] : 22;
    if ($targetPort <= 0) {
        $targetPort = 22;
    }
    $sshUser = isset($controlAccess['ssh_user']) ? trim((string)$controlAccess['ssh_user']) : 'root';
    if ($sshUser === '') {
        $sshUser = 'root';
    }
    $sshPassword = programmit_vpn_server_plain_ssh_password($db, $serverRow);
    $activateTimestamp = date('Y-m-d H:i:s');

    if ($targetHost === '') {
        $errorMessage = 'Este servidor no tiene host o IP válida para conexión SSH.';
        if ($actionName === 'activate' && $serverId > 0) {
            programmit_vpn_server_update_control_access($db, $serverId, array(
                'last_activate_at' => $activateTimestamp,
                'last_activate_status' => 'blocked'
            ));
        }
        return false;
    }
    if ($sshPassword === '') {
        $errorMessage = 'Guarda primero la contraseña SSH del nodo para habilitar la automatización.';
        if ($actionName === 'activate' && $serverId > 0) {
            programmit_vpn_server_update_control_access($db, $serverId, array(
                'last_activate_at' => $activateTimestamp,
                'last_activate_status' => 'blocked'
            ));
        }
        return false;
    }
    if (!$remoteExecSupported) {
        $errorMessage = $remoteExecReason !== '' ? $remoteExecReason : 'La ejecución SSH automática no está disponible en este entorno.';
        if ($actionName === 'activate' && $serverId > 0) {
            programmit_vpn_server_update_control_access($db, $serverId, array(
                'last_activate_at' => $activateTimestamp,
                'last_activate_status' => 'blocked'
            ));
        }
        return false;
    }

    if ($actionName === 'test') {
        $stdout = '';
        $stderr = '';
        $exitCode = 1;
        $reason = '';
        $remoteActionTitle = 'Salida de prueba SSH';
        $ok = programmit_vpn_remote_password_ssh_exec(
            $targetHost,
            $targetPort,
            $sshUser,
            $sshPassword,
            programmit_vpn_control_remote_probe_command(),
            $stdout,
            $stderr,
            $exitCode,
            $reason
        );
        $remoteActionOutput = programmit_vpn_control_remote_output($stdout, $stderr);
        if ($serverId > 0) {
            programmit_vpn_server_update_control_access($db, $serverId, array(
                'last_test_at' => $activateTimestamp,
                'last_test_status' => $ok ? 'ok' : 'error'
            ));
        }
        if ($ok) {
            $successMessage = 'Conexión SSH validada contra ' . $targetHost . '.';
            $remoteActionStatus = 'success';
            return true;
        }
        $errorMessage = $reason !== '' ? $reason : 'La prueba SSH devolvió error.';
        $remoteActionStatus = 'error';
        return false;
    }

    $bundle = programmit_vpn_build_onboarding_bundle($db, $serverRow, '');
    $plainToken = isset($bundle['plain_token']) ? trim((string)$bundle['plain_token']) : '';
    if ($plainToken === '') {
        $errorMessage = 'Aún no hay token plano recuperable para este nodo. Edita el servidor y define o regenera el sync token para instalar el agente.';
        if ($actionName === 'activate' && $serverId > 0) {
            programmit_vpn_server_update_control_access($db, $serverId, array(
                'last_activate_at' => $activateTimestamp,
                'last_activate_status' => 'blocked'
            ));
        }
        return false;
    }

    $installReason = '';
    $installCommand = programmit_vpn_build_remote_agent_install_command(
        $db,
        $serverRow,
        isset($bundle['config_json']) ? $bundle['config_json'] : '{}',
        $installReason
    );
    if ($installCommand === '') {
        $errorMessage = $installReason !== '' ? $installReason : 'No se pudo preparar la instalación remota.';
        if ($actionName === 'activate' && $serverId > 0) {
            programmit_vpn_server_update_control_access($db, $serverId, array(
                'last_activate_at' => $activateTimestamp,
                'last_activate_status' => 'error'
            ));
        }
        if ($serverId > 0) {
            programmit_vpn_server_update_control_access($db, $serverId, array(
                'last_install_at' => $activateTimestamp,
                'last_install_status' => 'error'
            ));
        }
        return false;
    }

    if ($actionName === 'install') {
        $stdout = '';
        $stderr = '';
        $exitCode = 1;
        $reason = '';
        $remoteActionTitle = 'Salida de instalación automática';
        $ok = programmit_vpn_remote_password_ssh_exec($targetHost, $targetPort, $sshUser, $sshPassword, $installCommand, $stdout, $stderr, $exitCode, $reason);
        $remoteActionOutput = programmit_vpn_control_remote_output($stdout, $stderr);
        if ($serverId > 0) {
            programmit_vpn_server_update_control_access($db, $serverId, array(
                'last_install_at' => $activateTimestamp,
                'last_install_status' => $ok ? 'ok' : 'error'
            ));
        }
        if ($ok) {
            $successMessage = 'Agente instalado y activado en ' . $targetHost . '.';
            $remoteActionStatus = 'success';
            return true;
        }
        $errorMessage = $reason !== '' ? $reason : 'La instalación remota devolvió error.';
        $remoteActionStatus = 'error';
        return false;
    }

    $remoteActionTitle = 'Salida de activación 1 clic';
    $outputChunks = array();

    $probeStdout = '';
    $probeStderr = '';
    $probeExitCode = 1;
    $probeReason = '';
    $probeOk = programmit_vpn_remote_password_ssh_exec(
        $targetHost,
        $targetPort,
        $sshUser,
        $sshPassword,
        programmit_vpn_control_remote_probe_command(),
        $probeStdout,
        $probeStderr,
        $probeExitCode,
        $probeReason
    );
    $probeOutput = programmit_vpn_control_remote_output($probeStdout, $probeStderr);
    if ($probeOutput !== '') {
        $outputChunks[] = "== PRECHECK SSH ==\n" . $probeOutput;
    }
    if ($serverId > 0) {
        programmit_vpn_server_update_control_access($db, $serverId, array(
            'last_test_at' => $activateTimestamp,
            'last_test_status' => $probeOk ? 'ok' : 'error'
        ));
    }
    if (!$probeOk) {
        if ($serverId > 0) {
            programmit_vpn_server_update_control_access($db, $serverId, array(
                'last_activate_at' => $activateTimestamp,
                'last_activate_status' => 'error'
            ));
        }
        $remoteActionOutput = trim(implode("\n\n", $outputChunks));
        $errorMessage = $probeReason !== '' ? $probeReason : 'La prueba SSH devolvió error.';
        $remoteActionStatus = 'error';
        return false;
    }

    $installStdout = '';
    $installStderr = '';
    $installExitCode = 1;
    $installExecReason = '';
    $installOk = programmit_vpn_remote_password_ssh_exec($targetHost, $targetPort, $sshUser, $sshPassword, $installCommand, $installStdout, $installStderr, $installExitCode, $installExecReason);
    $installOutput = programmit_vpn_control_remote_output($installStdout, $installStderr);
    if ($installOutput !== '') {
        $outputChunks[] = "== INSTALACION Y ARRANQUE ==\n" . $installOutput;
    }
    if ($serverId > 0) {
        programmit_vpn_server_update_control_access($db, $serverId, array(
            'last_install_at' => $activateTimestamp,
            'last_install_status' => $installOk ? 'ok' : 'error',
            'last_activate_at' => $activateTimestamp,
            'last_activate_status' => $installOk ? 'ok' : 'error'
        ));
    }

    $remoteActionOutput = trim(implode("\n\n", $outputChunks));
    if ($installOk) {
        $successMessage = 'Nodo activado en ' . $targetHost . '. SSH validado, agente instalado y timer encendido.';
        $remoteActionStatus = 'success';
        return true;
    }

    $errorMessage = $installExecReason !== '' ? $installExecReason : 'La activación automática devolvió error.';
    $remoteActionStatus = 'error';
    return false;
}

$vpn_error = '';
$vpn_success = '';
$vpn_generated_token = '';
$vpn_saved_server_id = 0;
$vpn_remote_action_title = '';
$vpn_remote_action_output = '';
$vpn_remote_action_status = '';
$vpn_remote_exec_env = programmit_vpn_remote_exec_environment();
$vpn_remote_exec_reason = '';
$vpn_remote_exec_supported = programmit_vpn_remote_password_ssh_supported($vpn_remote_exec_reason);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_vpn_server'])) {
    $serverId = isset($_POST['server_id']) ? (int)$_POST['server_id'] : 0;
    $saveAndActivate = isset($_POST['save_activate_vpn_server']);
    $serverAddress = trim((string)(isset($_POST['server_address']) ? $_POST['server_address'] : ''));
    $serverKey = programmit_vpn_normalize_key(isset($_POST['server_key']) ? $_POST['server_key'] : '');
    $serverName = trim((string)$_POST['server_name']);
    $serverHost = programmit_vpn_normalize_host(isset($_POST['server_host']) ? $_POST['server_host'] : '');
    $serverIp = trim((string)$_POST['server_ip']);
    $serverPort = isset($_POST['server_port']) ? (int)$_POST['server_port'] : 22;
    $serverProvider = programmit_vpn_normalize_key(isset($_POST['server_provider']) ? $_POST['server_provider'] : 'custom');
    $legacyCategory = programmit_vpn_normalize_key(isset($_POST['legacy_category']) ? $_POST['legacy_category'] : '');
    $countryCode = strtoupper(trim((string)$_POST['country_code']));
    $locationLabel = trim((string)$_POST['location_label']);
    $publicBaseUrl = trim((string)$_POST['public_base_url']);
    $status = strtolower(trim((string)$_POST['status']));
    $syncEnabled = isset($_POST['sync_enabled']) ? 1 : 0;
    $isPublic = isset($_POST['is_public']) ? 1 : 0;
    $syncToken = trim((string)$_POST['sync_token']);
    $publicPayloadJson = trim((string)$_POST['public_payload_json']);
    $metaJson = trim((string)$_POST['meta_json']);
    $sshUser = programmit_vpn_value_string(isset($_POST['ssh_user']) ? $_POST['ssh_user'] : 'root', 64);
    if ($sshUser === '') {
        $sshUser = 'root';
    }
    $sshPassword = (string)(isset($_POST['ssh_password']) ? $_POST['ssh_password'] : '');
    $existingServerMeta = array();

    if ($serverId > 0) {
        $existingServerQry = $db->sql_query("SELECT meta_json
            FROM vpn_servers
            WHERE id='" . $db->SanitizeForSQL($serverId) . "'
            LIMIT 1");
        $existingServerRow = $db->sql_fetchrow($existingServerQry);
        if ($existingServerRow) {
            $existingServerMeta = programmit_vpn_json_decode(isset($existingServerRow['meta_json']) ? $existingServerRow['meta_json'] : '');
        }
    }

    if ($serverAddress !== '') {
        $addressSource = $serverAddress;
        $parsedAddress = @parse_url(preg_match('/^[a-z][a-z0-9+.\-]*:\/\//i', $addressSource) ? $addressSource : ('ssh://' . $addressSource));
        $addressHost = '';
        $addressPort = 0;
        $addressScheme = '';

        if (is_array($parsedAddress)) {
            $addressHost = trim((string)(isset($parsedAddress['host']) ? $parsedAddress['host'] : ''));
            $addressPort = isset($parsedAddress['port']) ? (int)$parsedAddress['port'] : 0;
            $addressScheme = strtolower(trim((string)(isset($parsedAddress['scheme']) ? $parsedAddress['scheme'] : '')));
        }

        if ($addressHost === '') {
            $addressHost = trim((string)preg_replace('/\/.*$/', '', preg_replace('/^[a-z][a-z0-9+.\-]*:\/\//i', '', $addressSource)));
        }

        if ($addressHost !== '') {
            if ($serverHost === '' && $serverIp === '') {
                if (programmit_vpn_is_ip($addressHost)) {
                    $serverIp = $addressHost;
                } elseif (programmit_vpn_is_host_like($addressHost)) {
                    $serverHost = programmit_vpn_normalize_host($addressHost);
                }
            }

            if ($publicBaseUrl === '' && in_array($addressScheme, array('http', 'https'), true) && programmit_vpn_is_host_like($addressHost)) {
                $publicBaseUrl = $addressScheme . '://' . $addressHost;
                if ($addressPort > 0 && !(
                    ($addressScheme === 'http' && $addressPort === 80)
                    || ($addressScheme === 'https' && $addressPort === 443)
                )) {
                    $publicBaseUrl .= ':' . $addressPort;
                }
            }
        }

        if ($addressPort > 0 && (!isset($_POST['server_port']) || trim((string)$_POST['server_port']) === '')) {
            $serverPort = $addressPort;
        }
    }

    if ($serverName === '') {
        $vpn_error = 'Nombre del servidor obligatorio.';
    } else {
        if ($serverKey === '') {
            $serverKey = programmit_vpn_guess_server_key($serverName, $serverIp, $legacyCategory);
        }
        if ($serverKey === '') {
            $vpn_error = 'Clave de servidor invalida.';
        } elseif ($serverHost !== '' && !programmit_vpn_is_host_like($serverHost)) {
            $vpn_error = 'Host del servidor invalido.';
        } elseif ($serverIp !== '' && !programmit_vpn_is_ip($serverIp)) {
            $vpn_error = 'IP del servidor invalida.';
        } elseif ($serverPort <= 0 || $serverPort > 65535) {
            $vpn_error = 'Puerto del servidor invalido.';
        } elseif ($publicBaseUrl !== '' && !programmit_vpn_is_http_url($publicBaseUrl)) {
            $vpn_error = 'Public base URL invalida.';
        } elseif (!programmit_vpn_json_is_valid($publicPayloadJson)) {
            $vpn_error = 'Payload publico JSON invalido.';
        } elseif (!programmit_vpn_json_is_valid($metaJson)) {
            $vpn_error = 'Meta JSON interno invalido.';
        } elseif ($status === '' || !in_array($status, array('active', 'maintenance', 'disabled'), true)) {
            $vpn_error = 'Estado de servidor invalido.';
        } else {
            $dupSql = "SELECT id FROM vpn_servers
                WHERE server_key='".$db->SanitizeForSQL($serverKey)."'";
            if ($serverId > 0) {
                $dupSql .= " AND id<>'".$db->SanitizeForSQL($serverId)."'";
            }
            $dupSql .= " LIMIT 1";
            $dupQry = $db->sql_query($dupSql);
            if ($dupQry && $db->sql_numrows($dupQry) > 0) {
                $vpn_error = 'La clave del servidor ya existe.';
            } else {
                $syncTokenHash = '';
                if ($syncToken !== '') {
                    $syncTokenHash = programmit_vpn_hash_token($syncToken);
                    $vpn_generated_token = $syncToken;
                } elseif ($serverId <= 0) {
                    $vpn_generated_token = programmit_vpn_generate_token(40);
                    $syncTokenHash = programmit_vpn_hash_token($vpn_generated_token);
                }

                $publicPayload = programmit_vpn_json_decode($publicPayloadJson);
                $metaPayload = programmit_vpn_json_decode($metaJson);
                $existingControl = (isset($existingServerMeta['control_access']) && is_array($existingServerMeta['control_access']))
                    ? $existingServerMeta['control_access']
                    : array();
                $plainTokenForAccess = trim((string)$vpn_generated_token);
                if ($plainTokenForAccess === '' && $syncToken !== '') {
                    $plainTokenForAccess = $syncToken;
                }
                if ($plainTokenForAccess === '' && isset($existingControl['sync_token_enc'])) {
                    $plainTokenForAccess = programmit_vpn_secret_decrypt($db, $existingControl['sync_token_enc']);
                }
                $sshPasswordEnc = isset($existingControl['ssh_password_enc']) ? trim((string)$existingControl['ssh_password_enc']) : '';
                if ($sshPassword !== '') {
                    $sshPasswordEnc = programmit_vpn_secret_encrypt($db, $sshPassword);
                }
                $metaPayload['control_access'] = $existingControl;
                $metaPayload['control_access']['ssh_user'] = $sshUser;
                $metaPayload['control_access']['ssh_port'] = $serverPort > 0 ? $serverPort : 22;
                $metaPayload['control_access']['host_hint'] = $serverHost;
                $metaPayload['control_access']['ip_hint'] = $serverIp;
                $metaPayload['control_access']['updated_at'] = date('Y-m-d H:i:s');
                if ($sshPasswordEnc !== '') {
                    $metaPayload['control_access']['ssh_password_enc'] = $sshPasswordEnc;
                }
                if ($plainTokenForAccess !== '') {
                    $metaPayload['control_access']['sync_token_enc'] = programmit_vpn_secret_encrypt($db, $plainTokenForAccess);
                }
                $serverCompatSetSql = '';
                $serverCompatInsertColumns = '';
                $serverCompatInsertValues = '';
                $savedServerId = 0;

                if (programmit_vpn_table_has_column($db, 'vpn_servers', 'provider')) {
                    $serverCompatSetSql .= ", provider='" . $db->SanitizeForSQL($serverProvider !== '' ? $serverProvider : 'custom') . "'";
                    $serverCompatInsertColumns .= ", provider";
                    $serverCompatInsertValues .= ", '" . $db->SanitizeForSQL($serverProvider !== '' ? $serverProvider : 'custom') . "'";
                }
                if (programmit_vpn_table_has_column($db, 'vpn_servers', 'region')) {
                    $serverCompatSetSql .= ", region='" . $db->SanitizeForSQL($locationLabel) . "'";
                    $serverCompatInsertColumns .= ", region";
                    $serverCompatInsertValues .= ", '" . $db->SanitizeForSQL($locationLabel) . "'";
                }
                if (programmit_vpn_table_has_column($db, 'vpn_servers', 'public_host')) {
                    $serverCompatSetSql .= ", public_host='" . $db->SanitizeForSQL($serverHost) . "'";
                    $serverCompatInsertColumns .= ", public_host";
                    $serverCompatInsertValues .= ", '" . $db->SanitizeForSQL($serverHost) . "'";
                }
                if (programmit_vpn_table_has_column($db, 'vpn_servers', 'public_ip')) {
                    $serverCompatSetSql .= ", public_ip='" . $db->SanitizeForSQL($serverIp) . "'";
                    $serverCompatInsertColumns .= ", public_ip";
                    $serverCompatInsertValues .= ", '" . $db->SanitizeForSQL($serverIp) . "'";
                }
                if (programmit_vpn_table_has_column($db, 'vpn_servers', 'api_base_url')) {
                    $serverCompatSetSql .= ", api_base_url='" . $db->SanitizeForSQL($publicBaseUrl) . "'";
                    $serverCompatInsertColumns .= ", api_base_url";
                    $serverCompatInsertValues .= ", '" . $db->SanitizeForSQL($publicBaseUrl) . "'";
                }
                if (programmit_vpn_table_has_column($db, 'vpn_servers', 'is_sync_enabled')) {
                    $serverCompatSetSql .= ", is_sync_enabled='" . $db->SanitizeForSQL($syncEnabled) . "'";
                    $serverCompatInsertColumns .= ", is_sync_enabled";
                    $serverCompatInsertValues .= ", '" . $db->SanitizeForSQL($syncEnabled) . "'";
                }

                if ($serverId > 0) {
                    $setTokenSql = '';
                    if ($syncTokenHash !== '') {
                        $setTokenSql = ", sync_token_hash='".$db->SanitizeForSQL($syncTokenHash)."'";
                    }
                    $ok = $db->sql_query("UPDATE vpn_servers
                        SET server_key='".$db->SanitizeForSQL($serverKey)."',
                            server_name='".$db->SanitizeForSQL($serverName)."',
                            server_host='".$db->SanitizeForSQL($serverHost)."',
                            server_ip='".$db->SanitizeForSQL($serverIp)."',
                            server_port='".$db->SanitizeForSQL($serverPort)."',
                            server_provider='".$db->SanitizeForSQL($serverProvider !== '' ? $serverProvider : 'custom')."',
                            legacy_category='".$db->SanitizeForSQL($legacyCategory)."',
                            country_code='".$db->SanitizeForSQL(substr($countryCode, 0, 8))."',
                            location_label='".$db->SanitizeForSQL($locationLabel)."',
                            public_base_url='".$db->SanitizeForSQL($publicBaseUrl)."',
                            sync_enabled='".$db->SanitizeForSQL($syncEnabled)."',
                            is_public='".$db->SanitizeForSQL($isPublic)."',
                            status='".$db->SanitizeForSQL($status)."',
                            public_payload_json='".$db->SanitizeForSQL(programmit_vpn_json_encode($publicPayload))."',
                            meta_json='".$db->SanitizeForSQL(programmit_vpn_json_encode($metaPayload))."'
                            ".$serverCompatSetSql."
                            ".$setTokenSql.",
                            updated_at=NOW()
                        WHERE id='".$db->SanitizeForSQL($serverId)."'
                        LIMIT 1");
                    $vpn_success = $ok ? 'Servidor actualizado exitosamente.' : '';
                    if (!$ok) {
                        $vpn_error = 'No se pudo actualizar el servidor.';
                    } else {
                        $savedServerId = $serverId;
                        $vpn_saved_server_id = $savedServerId;
                    }
                } else {
                    $idParts = programmit_vpn_insert_id_parts($db, 'vpn_servers', 'id');
                    $ok = $db->sql_query("INSERT INTO vpn_servers
                        (server_key, server_name, server_host, server_ip, server_port, server_provider, legacy_category,
                         country_code, location_label, public_base_url, sync_token_hash, sync_enabled, is_public, status,
                         public_payload_json, meta_json, created_at, updated_at".$serverCompatInsertColumns.$idParts['columns'].")
                        VALUES
                        ('".$db->SanitizeForSQL($serverKey)."',
                         '".$db->SanitizeForSQL($serverName)."',
                         '".$db->SanitizeForSQL($serverHost)."',
                         '".$db->SanitizeForSQL($serverIp)."',
                         '".$db->SanitizeForSQL($serverPort)."',
                         '".$db->SanitizeForSQL($serverProvider !== '' ? $serverProvider : 'custom')."',
                         '".$db->SanitizeForSQL($legacyCategory)."',
                         '".$db->SanitizeForSQL(substr($countryCode, 0, 8))."',
                         '".$db->SanitizeForSQL($locationLabel)."',
                         '".$db->SanitizeForSQL($publicBaseUrl)."',
                         '".$db->SanitizeForSQL($syncTokenHash)."',
                         '".$db->SanitizeForSQL($syncEnabled)."',
                         '".$db->SanitizeForSQL($isPublic)."',
                         '".$db->SanitizeForSQL($status)."',
                         '".$db->SanitizeForSQL(programmit_vpn_json_encode($publicPayload))."',
                         '".$db->SanitizeForSQL(programmit_vpn_json_encode($metaPayload))."',
                          NOW(),
                          NOW()".$serverCompatInsertValues.$idParts['values'].")");
                    $vpn_success = $ok ? 'Servidor guardado exitosamente.' : '';
                    if (!$ok) {
                        $vpn_error = 'No se pudo crear el servidor.';
                    } else {
                        $savedServerId = method_exists($db, 'sql_nextid') ? (int)$db->sql_nextid() : 0;
                        if ($savedServerId <= 0) {
                            $savedQry = $db->sql_query("SELECT id
                                FROM vpn_servers
                                WHERE server_key='" . $db->SanitizeForSQL($serverKey) . "'
                                ORDER BY id DESC
                                LIMIT 1");
                            $savedRow = $db->sql_fetchrow($savedQry);
                            if ($savedRow && isset($savedRow['id'])) {
                                $savedServerId = (int)$savedRow['id'];
                            }
                        }
                        $vpn_saved_server_id = $savedServerId;
                    }
                }

                if ($vpn_error === '' && $savedServerId > 0) {
                    $legacyBridge = programmit_vpn_sync_server_to_legacy($db, $savedServerId);
                    if (!$legacyBridge['ok']) {
                        $vpn_error = 'Servidor guardado, pero fallo el puente legacy: ' . (isset($legacyBridge['message']) ? $legacyBridge['message'] : 'error no especificado');
                    } elseif (isset($legacyBridge['message']) && trim((string)$legacyBridge['message']) !== '') {
                        $vpn_success .= ' ' . trim((string)$legacyBridge['message']);
                    }
                }

                if ($vpn_error === '' && $savedServerId > 0 && $saveAndActivate) {
                    $activationRows = programmit_vpn_list_servers($db);
                    $activationServer = programmit_vpn_control_find_by_id($activationRows, $savedServerId);
                    $activationSuccess = '';
                    $activationError = '';
                    if (!$activationServer) {
                        $vpn_error = 'Servidor guardado, pero no se pudo recargar para activarlo.';
                    } else {
                        $activationOk = programmit_vpn_control_run_remote_action(
                            $db,
                            $activationServer,
                            'activate',
                            $vpn_remote_exec_supported,
                            $vpn_remote_exec_reason,
                            $vpn_remote_action_title,
                            $vpn_remote_action_output,
                            $vpn_remote_action_status,
                            $activationSuccess,
                            $activationError
                        );
                        if ($activationOk) {
                            $vpn_success = trim($vpn_success . ' ' . $activationSuccess);
                        } elseif ($activationError !== '') {
                            $vpn_error = ($vpn_success !== '' ? 'Servidor guardado, pero la activación automática falló. ' : '') . $activationError;
                        }
                    }
                }
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_vpn_method'])) {
    $methodId = isset($_POST['method_id']) ? (int)$_POST['method_id'] : 0;
    $methodKey = programmit_vpn_normalize_key(isset($_POST['method_key']) ? $_POST['method_key'] : '');
    $methodName = trim((string)$_POST['method_name']);
    $methodType = programmit_vpn_normalize_key(isset($_POST['method_type']) ? $_POST['method_type'] : 'custom');
    $legacyGroup = programmit_vpn_normalize_key(isset($_POST['legacy_group']) ? $_POST['legacy_group'] : '');
    $authMode = programmit_vpn_normalize_key(isset($_POST['auth_mode']) ? $_POST['auth_mode'] : 'local');
    $sortOrder = isset($_POST['sort_order']) ? (int)$_POST['sort_order'] : 100;
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    $isPublic = isset($_POST['is_public']) ? 1 : 0;
    $configJson = trim((string)$_POST['config_json']);
    $existingMethod = null;

    if ($methodKey === '' || $methodName === '') {
        $vpn_error = 'Clave y nombre del metodo son obligatorios.';
    } elseif (!programmit_vpn_json_is_valid($configJson)) {
        $vpn_error = 'Config JSON del metodo invalido.';
    } else {
        if ($methodId > 0) {
            $existingQry = $db->sql_query("SELECT id, method_key
                FROM vpn_methods
                WHERE id='".$db->SanitizeForSQL($methodId)."'
                LIMIT 1");
            $existingMethod = $db->sql_fetchrow($existingQry);
        }
        if ($existingMethod && in_array((string)$existingMethod['method_key'], array('premium', 'vip', 'private', 'free'), true) && (string)$existingMethod['method_key'] !== $methodKey) {
            $vpn_error = 'No puedes cambiar la clave de un metodo legacy base.';
        }
    }

    if ($vpn_error === '') {
        $dupSql = "SELECT id FROM vpn_methods
            WHERE method_key='".$db->SanitizeForSQL($methodKey)."'";
        if ($methodId > 0) {
            $dupSql .= " AND id<>'".$db->SanitizeForSQL($methodId)."'";
        }
        $dupSql .= " LIMIT 1";
        $dupQry = $db->sql_query($dupSql);
        if ($dupQry && $db->sql_numrows($dupQry) > 0) {
            $vpn_error = 'La clave del metodo ya existe.';
        } else {
            $configPayload = programmit_vpn_json_decode($configJson);
            $methodCompatSetSql = '';
            $methodCompatInsertColumns = '';
            $methodCompatInsertValues = '';

            if (programmit_vpn_table_has_column($db, 'vpn_methods', 'category')) {
                $methodCompatSetSql .= ", category='" . $db->SanitizeForSQL($methodType !== '' ? $methodType : 'custom') . "'";
                $methodCompatInsertColumns .= ", category";
                $methodCompatInsertValues .= ", '" . $db->SanitizeForSQL($methodType !== '' ? $methodType : 'custom') . "'";
            }
            if (programmit_vpn_table_has_column($db, 'vpn_methods', 'auth_strategy')) {
                $methodCompatSetSql .= ", auth_strategy='" . $db->SanitizeForSQL($authMode !== '' ? $authMode : 'local') . "'";
                $methodCompatInsertColumns .= ", auth_strategy";
                $methodCompatInsertValues .= ", '" . $db->SanitizeForSQL($authMode !== '' ? $authMode : 'local') . "'";
            }
            if (programmit_vpn_table_has_column($db, 'vpn_methods', 'delivery_strategy')) {
                $methodCompatSetSql .= ", delivery_strategy='pull_json'";
                $methodCompatInsertColumns .= ", delivery_strategy";
                $methodCompatInsertValues .= ", 'pull_json'";
            }
            if (programmit_vpn_table_has_column($db, 'vpn_methods', 'ui_config_json')) {
                $methodCompatSetSql .= ", ui_config_json='" . $db->SanitizeForSQL(programmit_vpn_json_encode($configPayload)) . "'";
                $methodCompatInsertColumns .= ", ui_config_json";
                $methodCompatInsertValues .= ", '" . $db->SanitizeForSQL(programmit_vpn_json_encode($configPayload)) . "'";
            }
            if (programmit_vpn_table_has_column($db, 'vpn_methods', 'policy_json')) {
                $methodCompatSetSql .= ", policy_json='" . $db->SanitizeForSQL(programmit_vpn_json_encode($configPayload)) . "'";
                $methodCompatInsertColumns .= ", policy_json";
                $methodCompatInsertValues .= ", '" . $db->SanitizeForSQL(programmit_vpn_json_encode($configPayload)) . "'";
            }

            if ($methodId > 0) {
                $ok = $db->sql_query("UPDATE vpn_methods
                    SET method_key='".$db->SanitizeForSQL($methodKey)."',
                        method_name='".$db->SanitizeForSQL($methodName)."',
                        method_type='".$db->SanitizeForSQL($methodType !== '' ? $methodType : 'custom')."',
                        legacy_group='".$db->SanitizeForSQL($legacyGroup)."',
                        auth_mode='".$db->SanitizeForSQL($authMode !== '' ? $authMode : 'local')."',
                        is_active='".$db->SanitizeForSQL($isActive)."',
                        is_public='".$db->SanitizeForSQL($isPublic)."',
                        sort_order='".$db->SanitizeForSQL($sortOrder)."',
                        config_json='".$db->SanitizeForSQL(programmit_vpn_json_encode($configPayload))."'
                        ".$methodCompatSetSql.",
                        updated_at=NOW()
                    WHERE id='".$db->SanitizeForSQL($methodId)."'
                    LIMIT 1");
                $vpn_success = $ok ? 'Metodo actualizado.' : '';
                if (!$ok) {
                    $vpn_error = 'No se pudo actualizar el metodo.';
                } elseif ($existingMethod && (string)$existingMethod['method_key'] !== $methodKey) {
                    $db->sql_query("UPDATE vpn_user_method_assignments
                        SET method_key='".$db->SanitizeForSQL($methodKey)."',
                            updated_at=NOW()
                        WHERE method_key='".$db->SanitizeForSQL((string)$existingMethod['method_key'])."'");
                    $db->sql_query("UPDATE vpn_servers
                        SET last_sync_cursor='0',
                            last_sync_at=NULL,
                            last_ack_at=NULL,
                            updated_at=NOW()
                        WHERE id IN (
                            SELECT server_id
                            FROM vpn_method_server_map
                            WHERE method_id='".$db->SanitizeForSQL($methodId)."'
                        )");
                    programmit_vpn_set_setting($db, 'vpn_reconcile_last_run_ts', '0');
                }
            } else {
                $idParts = programmit_vpn_insert_id_parts($db, 'vpn_methods', 'id');
                $ok = $db->sql_query("INSERT INTO vpn_methods
                    (method_key, method_name, method_type, legacy_group, auth_mode, is_active, is_public, sort_order, config_json, created_at, updated_at".$methodCompatInsertColumns.$idParts['columns'].")
                    VALUES
                    ('".$db->SanitizeForSQL($methodKey)."',
                     '".$db->SanitizeForSQL($methodName)."',
                     '".$db->SanitizeForSQL($methodType !== '' ? $methodType : 'custom')."',
                     '".$db->SanitizeForSQL($legacyGroup)."',
                     '".$db->SanitizeForSQL($authMode !== '' ? $authMode : 'local')."',
                     '".$db->SanitizeForSQL($isActive)."',
                     '".$db->SanitizeForSQL($isPublic)."',
                     '".$db->SanitizeForSQL($sortOrder)."',
                     '".$db->SanitizeForSQL(programmit_vpn_json_encode($configPayload))."',
                     NOW(),
                     NOW()".$methodCompatInsertValues.$idParts['values'].")");
                $vpn_success = $ok ? 'Metodo creado.' : '';
                if (!$ok) {
                    $vpn_error = 'No se pudo crear el metodo.';
                }
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_vpn_mapping'])) {
    $mapId = isset($_POST['map_id']) ? (int)$_POST['map_id'] : 0;
    $methodId = isset($_POST['method_id']) ? (int)$_POST['method_id'] : 0;
    $serverId = isset($_POST['server_id']) ? (int)$_POST['server_id'] : 0;
    $endpointProtocol = strtolower(trim((string)$_POST['endpoint_protocol']));
    $endpointHost = programmit_vpn_normalize_host(isset($_POST['endpoint_host']) ? $_POST['endpoint_host'] : '');
    $endpointPort = isset($_POST['endpoint_port']) ? (int)$_POST['endpoint_port'] : 443;
    $deployPath = trim((string)$_POST['deploy_path']);
    $tlsSni = trim((string)$_POST['tls_sni']);
    $weight = isset($_POST['weight']) ? (int)$_POST['weight'] : 100;
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    $isDefault = isset($_POST['is_default']) ? 1 : 0;
    $configJson = trim((string)$_POST['config_json']);
    $existingMap = null;

    if ($mapId > 0) {
        $existingMapQry = $db->sql_query("SELECT ms.id, ms.method_id, ms.server_id, ms.is_active,
                m.method_key
            FROM vpn_method_server_map ms
            INNER JOIN vpn_methods m ON m.id=ms.method_id
            WHERE ms.id='".$db->SanitizeForSQL($mapId)."'
            LIMIT 1");
        $existingMap = $db->sql_fetchrow($existingMapQry);
    }

    if ($methodId <= 0 || $serverId <= 0) {
        $vpn_error = 'Debes seleccionar metodo y servidor.';
    } elseif (!in_array($endpointProtocol, array('https', 'http', 'tcp', 'udp'), true)) {
        $vpn_error = 'Protocolo de despliegue invalido.';
    } elseif ($endpointHost !== '' && !programmit_vpn_is_host_like($endpointHost)) {
        $vpn_error = 'Host endpoint invalido.';
    } elseif ($endpointPort <= 0 || $endpointPort > 65535) {
        $vpn_error = 'Puerto de despliegue invalido.';
    } elseif (!programmit_vpn_json_is_valid($configJson)) {
        $vpn_error = 'Config JSON de la relacion invalido.';
    } else {
        $dupSql = "SELECT id FROM vpn_method_server_map
            WHERE method_id='".$db->SanitizeForSQL($methodId)."'
              AND server_id='".$db->SanitizeForSQL($serverId)."'";
        if ($mapId > 0) {
            $dupSql .= " AND id<>'".$db->SanitizeForSQL($mapId)."'";
        }
        $dupSql .= " LIMIT 1";
        $dupQry = $db->sql_query($dupSql);
        if ($dupQry && $db->sql_numrows($dupQry) > 0) {
            $vpn_error = 'Ese metodo ya esta relacionado con ese servidor.';
        } else {
            $configPayload = programmit_vpn_json_decode($configJson);
            if ($isDefault === 1) {
                $db->sql_query("UPDATE vpn_method_server_map
                    SET is_default='0', updated_at=NOW()
                    WHERE method_id='".$db->SanitizeForSQL($methodId)."'");
            }
            if ($mapId > 0) {
                $ok = $db->sql_query("UPDATE vpn_method_server_map
                    SET method_id='".$db->SanitizeForSQL($methodId)."',
                        server_id='".$db->SanitizeForSQL($serverId)."',
                        endpoint_protocol='".$db->SanitizeForSQL($endpointProtocol)."',
                        endpoint_host='".$db->SanitizeForSQL($endpointHost)."',
                        endpoint_port='".$db->SanitizeForSQL($endpointPort)."',
                        deploy_path='".$db->SanitizeForSQL($deployPath)."',
                        tls_sni='".$db->SanitizeForSQL($tlsSni)."',
                        weight='".$db->SanitizeForSQL($weight)."',
                        is_active='".$db->SanitizeForSQL($isActive)."',
                        is_default='".$db->SanitizeForSQL($isDefault)."',
                        config_json='".$db->SanitizeForSQL(programmit_vpn_json_encode($configPayload))."',
                        updated_at=NOW()
                    WHERE id='".$db->SanitizeForSQL($mapId)."'
                    LIMIT 1");
                $vpn_success = $ok ? 'Relacion metodo-servidor actualizada.' : '';
                if (!$ok) {
                    $vpn_error = 'No se pudo actualizar la relacion.';
                } else {
                    if ($existingMap && isset($existingMap['server_id'], $existingMap['method_key'])) {
                        $oldServerId = (int)$existingMap['server_id'];
                        $oldMethodId = isset($existingMap['method_id']) ? (int)$existingMap['method_id'] : 0;
                        $oldMethodKey = programmit_vpn_normalize_key(isset($existingMap['method_key']) ? $existingMap['method_key'] : '');
                        $oldIsActive = isset($existingMap['is_active']) ? (int)$existingMap['is_active'] : 0;
                        if ($oldServerId > 0 && $oldMethodKey !== '' && ($oldServerId !== $serverId || $oldMethodId !== $methodId || ($oldIsActive === 1 && $isActive !== 1))) {
                            programmit_vpn_create_targeted_event($db, 'purge', array($oldServerId), array(
                                'purge' => 1,
                                'scope' => 'method',
                                'method_key' => $oldMethodKey,
                                'reason' => 'mapping_changed'
                            ), array(), array($oldMethodKey));
                        }
                    }
                    $db->sql_query("UPDATE vpn_servers
                        SET last_sync_cursor='0',
                            last_sync_at=NULL,
                            last_ack_at=NULL,
                            updated_at=NOW()
                        WHERE id='".$db->SanitizeForSQL($serverId)."'");
                    programmit_vpn_set_setting($db, 'vpn_reconcile_last_run_ts', '0');
                }
            } else {
                $idParts = programmit_vpn_insert_id_parts($db, 'vpn_method_server_map', 'id');
                $ok = $db->sql_query("INSERT INTO vpn_method_server_map
                    (method_id, server_id, endpoint_protocol, endpoint_host, endpoint_port, deploy_path, tls_sni, weight, is_active, is_default, config_json, created_at, updated_at".$idParts['columns'].")
                    VALUES
                    ('".$db->SanitizeForSQL($methodId)."',
                     '".$db->SanitizeForSQL($serverId)."',
                     '".$db->SanitizeForSQL($endpointProtocol)."',
                     '".$db->SanitizeForSQL($endpointHost)."',
                     '".$db->SanitizeForSQL($endpointPort)."',
                     '".$db->SanitizeForSQL($deployPath)."',
                     '".$db->SanitizeForSQL($tlsSni)."',
                     '".$db->SanitizeForSQL($weight)."',
                     '".$db->SanitizeForSQL($isActive)."',
                     '".$db->SanitizeForSQL($isDefault)."',
                     '".$db->SanitizeForSQL(programmit_vpn_json_encode($configPayload))."',
                     NOW(),
                     NOW()".$idParts['values'].")");
                $vpn_success = $ok ? 'Relacion metodo-servidor creada.' : '';
                if (!$ok) {
                    $vpn_error = 'No se pudo crear la relacion.';
                } else {
                    $db->sql_query("UPDATE vpn_servers
                        SET last_sync_cursor='0',
                            last_sync_at=NULL,
                            last_ack_at=NULL,
                            updated_at=NOW()
                        WHERE id='".$db->SanitizeForSQL($serverId)."'");
                    programmit_vpn_set_setting($db, 'vpn_reconcile_last_run_ts', '0');
                }
            }
        }
    }
}

$vpn_reconcile_summary = array();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_vpn_reconcile'])) {
    $vpn_reconcile_summary = programmit_vpn_reconcile_users($db, true);
    if ($vpn_error === '') {
        $vpn_success = 'Reconciliacion ejecutada. Nuevos: '.(int)$vpn_reconcile_summary['created'].' | Actualizados: '.(int)$vpn_reconcile_summary['updated'].' | Eliminados: '.(int)$vpn_reconcile_summary['deleted'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['vpn_test_ssh']) || isset($_POST['vpn_install_agent']) || isset($_POST['vpn_activate_node']))) {
    $actionServerId = isset($_POST['server_id']) ? (int)$_POST['server_id'] : 0;
    $vpn_saved_server_id = $actionServerId;
    $actionRows = programmit_vpn_list_servers($db);
    $actionServer = programmit_vpn_control_find_by_id($actionRows, $actionServerId);
    $actionName = 'test';
    if (isset($_POST['vpn_activate_node'])) {
        $actionName = 'activate';
    } elseif (isset($_POST['vpn_install_agent'])) {
        $actionName = 'install';
    }

    if (!$actionServer) {
        $vpn_error = 'No se encontró el servidor para la acción remota.';
    } else {
        $actionSuccess = '';
        $actionError = '';
        $actionOk = programmit_vpn_control_run_remote_action(
            $db,
            $actionServer,
            $actionName,
            $vpn_remote_exec_supported,
            $vpn_remote_exec_reason,
            $vpn_remote_action_title,
            $vpn_remote_action_output,
            $vpn_remote_action_status,
            $actionSuccess,
            $actionError
        );
        if ($actionOk) {
            $vpn_success = $actionSuccess;
        } else {
            $vpn_error = $actionError;
        }
    }
}

$vpn_servers = programmit_vpn_list_servers($db);
$vpn_methods = programmit_vpn_list_methods($db);
$vpn_deployments = programmit_vpn_list_deployments($db);
$vpn_sync_logs = programmit_vpn_list_sync_logs($db, 80);
$vpn_public_catalog = programmit_vpn_public_catalog($db);
$vpn_reconcile_last_summary = programmit_vpn_json_decode(programmit_vpn_get_setting($db, 'vpn_reconcile_last_summary', '{}'));
$vpn_public_endpoint_key = trim((string)programmit_vpn_get_setting($db, 'vpn_public_app_endpoint_key', 'vpn-app-config'));

$editServerId = isset($_GET['edit_server']) ? (int)$_GET['edit_server'] : 0;
$editMethodId = isset($_GET['edit_method']) ? (int)$_GET['edit_method'] : 0;
$editMapId = isset($_GET['edit_map']) ? (int)$_GET['edit_map'] : 0;
$onboardServerId = isset($_GET['onboard_server']) ? (int)$_GET['onboard_server'] : 0;

if ($onboardServerId > 0) {
    $vpn_admin_view = 'create-server';
    $vpn_view_qs = $vpn_embed_qs . '&vpn_view=create-server';
}

$vpn_server_form = programmit_vpn_control_find_by_id($vpn_servers, $editServerId);
$vpn_method_form = programmit_vpn_control_find_by_id($vpn_methods, $editMethodId);
$vpn_map_form = programmit_vpn_control_find_by_id($vpn_deployments, $editMapId);

if (!$vpn_server_form) {
    $vpn_server_form = array(
        'id' => 0,
        'server_key' => '',
        'server_name' => '',
        'server_host' => '',
        'server_ip' => '',
        'server_port' => 22,
        'server_provider' => 'custom',
        'legacy_category' => '',
        'country_code' => '',
        'location_label' => '',
        'public_base_url' => '',
        'status' => 'active',
        'sync_enabled' => 1,
        'is_public' => 1,
        'public_payload_json' => '{}',
        'meta_json' => '{}',
        'ssh_user' => 'root',
        'ssh_password_saved' => 0,
        'sync_token_saved' => 0
    );
}

if ($vpn_server_form && is_array($vpn_server_form)) {
    $vpn_server_control_access = programmit_vpn_server_control_access($db, $vpn_server_form);
    $vpn_server_form['ssh_user'] = isset($vpn_server_control_access['ssh_user']) ? $vpn_server_control_access['ssh_user'] : 'root';
    $vpn_server_form['ssh_password_saved'] = !empty($vpn_server_control_access['has_password']) ? 1 : 0;
    $vpn_server_form['sync_token_saved'] = !empty($vpn_server_control_access['has_sync_token']) ? 1 : 0;
}

if ($vpn_error !== '' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_vpn_server'])) {
    $vpn_server_form = array_merge($vpn_server_form, array(
        'id' => isset($_POST['server_id']) ? (int)$_POST['server_id'] : 0,
        'server_key' => trim((string)(isset($_POST['server_key']) ? $_POST['server_key'] : '')),
        'server_name' => trim((string)(isset($_POST['server_name']) ? $_POST['server_name'] : '')),
        'server_host' => trim((string)(isset($_POST['server_host']) ? $_POST['server_host'] : $serverHost)),
        'server_ip' => trim((string)(isset($_POST['server_ip']) ? $_POST['server_ip'] : $serverIp)),
        'server_port' => isset($_POST['server_port']) ? (int)$_POST['server_port'] : 22,
        'server_provider' => trim((string)(isset($_POST['server_provider']) ? $_POST['server_provider'] : 'custom')),
        'legacy_category' => trim((string)(isset($_POST['legacy_category']) ? $_POST['legacy_category'] : '')),
        'country_code' => trim((string)(isset($_POST['country_code']) ? $_POST['country_code'] : '')),
        'location_label' => trim((string)(isset($_POST['location_label']) ? $_POST['location_label'] : '')),
        'public_base_url' => trim((string)(isset($_POST['public_base_url']) ? $_POST['public_base_url'] : '')),
        'status' => trim((string)(isset($_POST['status']) ? $_POST['status'] : 'active')),
        'sync_enabled' => isset($_POST['sync_enabled']) ? 1 : 0,
        'is_public' => isset($_POST['is_public']) ? 1 : 0,
        'public_payload_json' => trim((string)(isset($_POST['public_payload_json']) ? $_POST['public_payload_json'] : '{}')),
        'meta_json' => trim((string)(isset($_POST['meta_json']) ? $_POST['meta_json'] : '{}')),
        'ssh_user' => trim((string)(isset($_POST['ssh_user']) ? $_POST['ssh_user'] : 'root')),
        'ssh_password_saved' => trim((string)(isset($_POST['ssh_password']) ? $_POST['ssh_password'] : '')) !== '' ? 1 : (isset($vpn_server_form['ssh_password_saved']) ? (int)$vpn_server_form['ssh_password_saved'] : 0),
        'sync_token_saved' => trim((string)(isset($_POST['sync_token']) ? $_POST['sync_token'] : '')) !== '' ? 1 : (isset($vpn_server_form['sync_token_saved']) ? (int)$vpn_server_form['sync_token_saved'] : 0)
    ));
}

if (!$vpn_method_form) {
    $vpn_method_form = array(
        'id' => 0,
        'method_key' => '',
        'method_name' => '',
        'method_type' => 'custom',
        'legacy_group' => '',
        'auth_mode' => 'local',
        'sort_order' => 100,
        'is_active' => 1,
        'is_public' => 1,
        'config_json' => '{}'
    );
}

$vpn_onboarding_ready = false;
$vpn_onboarding_config_json = '';
$vpn_onboarding_install_script = '';
$vpn_onboarding_server_name = '';
$vpn_onboarding_server_target = '';
$vpn_onboarding_token_visible = false;
$vpn_onboarding_token_ready = false;
$vpn_onboarding_server_id = 0;
$vpn_onboarding_ssh_user = 'root';
$vpn_onboarding_ssh_ready = false;
$vpn_onboarding_ready_for_install = false;

$vpn_onboarding_source_id = $vpn_saved_server_id > 0 ? $vpn_saved_server_id : $onboardServerId;

if ($vpn_onboarding_source_id > 0) {
    $vpn_onboarding_server = programmit_vpn_control_find_by_id($vpn_servers, $vpn_onboarding_source_id);
    if ($vpn_onboarding_server) {
        $plainToken = trim((string)$vpn_generated_token);
        if ($plainToken === '' && isset($_POST['sync_token'])) {
            $plainToken = trim((string)$_POST['sync_token']);
        }
        $vpn_onboarding_bundle = programmit_vpn_build_onboarding_bundle($db, $vpn_onboarding_server, $plainToken);
        if (!empty($vpn_onboarding_bundle)) {
            $vpn_onboarding_server_id = isset($vpn_onboarding_bundle['server_id']) ? (int)$vpn_onboarding_bundle['server_id'] : 0;
            $vpn_onboarding_config_json = isset($vpn_onboarding_bundle['config_json']) ? (string)$vpn_onboarding_bundle['config_json'] : '{}';
            $vpn_onboarding_install_script = isset($vpn_onboarding_bundle['install_script']) ? (string)$vpn_onboarding_bundle['install_script'] : '';
            $vpn_onboarding_server_name = isset($vpn_onboarding_bundle['server_name']) ? (string)$vpn_onboarding_bundle['server_name'] : '';
            $vpn_onboarding_server_target = isset($vpn_onboarding_bundle['server_target']) ? (string)$vpn_onboarding_bundle['server_target'] : '';
            $vpn_onboarding_token_visible = !empty($vpn_onboarding_bundle['token_visible']);
            $vpn_onboarding_ssh_user = isset($vpn_onboarding_bundle['control_access']['ssh_user']) ? (string)$vpn_onboarding_bundle['control_access']['ssh_user'] : 'root';
            $vpn_onboarding_ssh_ready = !empty($vpn_onboarding_bundle['control_access']['has_password']);
            $vpn_onboarding_token_ready = !empty($vpn_onboarding_bundle['control_access']['has_sync_token']);
            $vpn_onboarding_ready_for_install = !empty($vpn_onboarding_bundle['control_access']['ready_for_install']);
            $vpn_onboarding_ready = true;
        }
    }
}

if (!$vpn_map_form) {
    $vpn_map_form = array(
        'id' => 0,
        'method_id' => 0,
        'server_id' => 0,
        'endpoint_protocol' => 'https',
        'endpoint_host' => '',
        'endpoint_port' => 443,
        'deploy_path' => '',
        'tls_sni' => '',
        'weight' => 100,
        'is_active' => 1,
        'is_default' => 0,
        'config_json' => '{}'
    );
}

$vpn_public_endpoint_url = $db->base_url() . 'api/vpn_app_manifest.php';
$vpn_public_endpoint_read_url = $vpn_public_endpoint_url;
if ($vpn_public_endpoint_key !== '') {
    $vpn_public_endpoint_read_url .= '?key=' . urlencode($vpn_public_endpoint_key);
}
$vpn_sync_pull_url = $db->base_url() . 'api/vpn_sync_pull.php';
$vpn_sync_ack_url = $db->base_url() . 'api/vpn_sync_ack.php';

$vpn_manage_summary = array(
    'total' => count($vpn_servers),
    'active' => 0,
    'automation_ready' => 0,
    'pending_setup' => 0,
    'sync_on' => 0,
    'healthy' => 0,
    'attention' => 0,
    'offline' => 0,
    'agent_reporting' => 0,
    'runtime_seen' => 0,
    'public_nodes' => 0,
    'methods' => count($vpn_methods),
    'deployments' => count($vpn_deployments)
);

foreach ((array)$vpn_servers as $vpnServerRow) {
    if (!is_array($vpnServerRow)) {
        continue;
    }
    if (isset($vpnServerRow['status']) && trim((string)$vpnServerRow['status']) === 'active') {
        $vpn_manage_summary['active']++;
    }
    if (isset($vpnServerRow['sync_enabled']) && (int)$vpnServerRow['sync_enabled'] === 1) {
        $vpn_manage_summary['sync_on']++;
    }
    if (isset($vpnServerRow['is_public']) && (int)$vpnServerRow['is_public'] === 1) {
        $vpn_manage_summary['public_nodes']++;
    }
    if (!empty($vpnServerRow['control_access']['ready_for_install'])) {
        $vpn_manage_summary['automation_ready']++;
    } else {
        $vpn_manage_summary['pending_setup']++;
    }
    if (isset($vpnServerRow['health_state']['class'])) {
        if ($vpnServerRow['health_state']['class'] === 'ok') {
            $vpn_manage_summary['healthy']++;
        } elseif ($vpnServerRow['health_state']['class'] === 'off') {
            $vpn_manage_summary['offline']++;
        } else {
            $vpn_manage_summary['attention']++;
        }
    }
    if (
        trim((string)(isset($vpnServerRow['last_seen_at']) ? $vpnServerRow['last_seen_at'] : '')) !== ''
        || trim((string)(isset($vpnServerRow['runtime_agent_version']) ? $vpnServerRow['runtime_agent_version'] : '')) !== ''
    ) {
        $vpn_manage_summary['runtime_seen']++;
    }
    if (trim((string)(isset($vpnServerRow['runtime_agent_version']) ? $vpnServerRow['runtime_agent_version'] : '')) !== '') {
        $vpn_manage_summary['agent_reporting']++;
    }
}

$smarty->assign('page', 'vpn-control');
$smarty->assign('vpn_admin_view', $vpn_admin_view);
$smarty->assign('vpn_embed_admin', $vpn_embed_admin ? 1 : 0);
$smarty->assign('vpn_embed_qs', $vpn_embed_qs);
$smarty->assign('vpn_view_qs', $vpn_view_qs);
$smarty->assign('vpn_error', $vpn_error);
$smarty->assign('vpn_success', $vpn_success);
$smarty->assign('vpn_generated_token', $vpn_generated_token);
$smarty->assign('vpn_servers', $vpn_servers);
$smarty->assign('vpn_methods', $vpn_methods);
$smarty->assign('vpn_deployments', $vpn_deployments);
$smarty->assign('vpn_sync_logs', $vpn_sync_logs);
$smarty->assign('vpn_public_catalog_json', json_encode($vpn_public_catalog, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
$smarty->assign('vpn_server_form', $vpn_server_form);
$smarty->assign('vpn_method_form', $vpn_method_form);
$smarty->assign('vpn_map_form', $vpn_map_form);
$smarty->assign('vpn_reconcile_last_summary', $vpn_reconcile_last_summary);
$smarty->assign('vpn_public_endpoint_key', $vpn_public_endpoint_key);
$smarty->assign('vpn_public_endpoint_url', $vpn_public_endpoint_url);
$smarty->assign('vpn_public_endpoint_read_url', $vpn_public_endpoint_read_url);
$smarty->assign('vpn_sync_pull_url', $vpn_sync_pull_url);
$smarty->assign('vpn_sync_ack_url', $vpn_sync_ack_url);
$smarty->assign('vpn_manage_summary', $vpn_manage_summary);
$smarty->assign('vpn_onboarding_ready', $vpn_onboarding_ready ? 1 : 0);
$smarty->assign('vpn_onboarding_config_json', $vpn_onboarding_config_json);
$smarty->assign('vpn_onboarding_install_script', $vpn_onboarding_install_script);
$smarty->assign('vpn_onboarding_server_name', $vpn_onboarding_server_name);
$smarty->assign('vpn_onboarding_server_target', $vpn_onboarding_server_target);
$smarty->assign('vpn_onboarding_server_id', $vpn_onboarding_server_id);
$smarty->assign('vpn_onboarding_token_visible', $vpn_onboarding_token_visible ? 1 : 0);
$smarty->assign('vpn_onboarding_token_ready', $vpn_onboarding_token_ready ? 1 : 0);
$smarty->assign('vpn_onboarding_ssh_user', $vpn_onboarding_ssh_user);
$smarty->assign('vpn_onboarding_ssh_ready', $vpn_onboarding_ssh_ready ? 1 : 0);
$smarty->assign('vpn_onboarding_ready_for_install', $vpn_onboarding_ready_for_install ? 1 : 0);
$smarty->assign('vpn_remote_exec_env', $vpn_remote_exec_env);
$smarty->assign('vpn_remote_exec_supported', $vpn_remote_exec_supported ? 1 : 0);
$smarty->assign('vpn_remote_exec_reason', $vpn_remote_exec_reason);
$smarty->assign('vpn_remote_action_title', $vpn_remote_action_title);
$smarty->assign('vpn_remote_action_output', $vpn_remote_action_output);
$smarty->assign('vpn_remote_action_status', $vpn_remote_action_status);
$smarty->display('vpn-control.tpl');
