<?php
chkSession();

$embed_raw = isset($_GET['embed']) ? strtolower(trim((string)$_GET['embed'])) : '';
$vpn_embed_admin = in_array($embed_raw, array('1', 'admin', 'yes'), true);
$vpn_embed_qs = $vpn_embed_admin ? '&embed=admin' : '';

if (!programmit_vpn_can_manage($user_id_2, $user_level_2)) {
    header("Location: ".$db->base_url()."index.php?p=dashboard");
    exit;
}

programmit_vpn_ensure_tables($db);

if (function_exists('programmit_saas_can_manage_from_current_host') && function_exists('programmit_saas_get_control_host')) {
    if (!programmit_saas_can_manage_from_current_host($db)) {
        header("Location: https://" . programmit_saas_get_control_host($db) . "/index.php?p=vpn-control" . $vpn_embed_qs);
        exit;
    }
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

$vpn_error = '';
$vpn_success = '';
$vpn_generated_token = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_vpn_server'])) {
    $serverId = isset($_POST['server_id']) ? (int)$_POST['server_id'] : 0;
    $serverKey = programmit_vpn_normalize_key(isset($_POST['server_key']) ? $_POST['server_key'] : '');
    $serverName = trim((string)$_POST['server_name']);
    $serverHost = programmit_vpn_normalize_host(isset($_POST['server_host']) ? $_POST['server_host'] : '');
    $serverIp = trim((string)$_POST['server_ip']);
    $serverPort = isset($_POST['server_port']) ? (int)$_POST['server_port'] : 443;
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

    if ($serverName === '') {
        $vpn_error = 'Nombre del servidor obligatorio.';
    } else {
        if ($serverKey === '') {
            $serverKey = programmit_vpn_guess_server_key($serverName, $serverIp, $legacyCategory);
        }
        if ($serverKey === '') {
            $vpn_error = 'Clave de servidor invalida.';
        } elseif ($serverPort <= 0 || $serverPort > 65535) {
            $vpn_error = 'Puerto del servidor invalido.';
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
                $serverCompatSetSql = '';
                $serverCompatInsertColumns = '';
                $serverCompatInsertValues = '';

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
                    $vpn_success = $ok ? 'Servidor actualizado.' : '';
                    if (!$ok) {
                        $vpn_error = 'No se pudo actualizar el servidor.';
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
                    $vpn_success = $ok ? 'Servidor creado.' : '';
                    if (!$ok) {
                        $vpn_error = 'No se pudo crear el servidor.';
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
    } elseif ($endpointPort <= 0 || $endpointPort > 65535) {
        $vpn_error = 'Puerto de despliegue invalido.';
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
        'server_port' => 443,
        'server_provider' => 'custom',
        'legacy_category' => '',
        'country_code' => '',
        'location_label' => '',
        'public_base_url' => '',
        'status' => 'active',
        'sync_enabled' => 1,
        'is_public' => 1,
        'public_payload_json' => '{}',
        'meta_json' => '{}'
    );
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

$smarty->assign('page', 'vpn-control');
$smarty->assign('vpn_embed_admin', $vpn_embed_admin ? 1 : 0);
$smarty->assign('vpn_embed_qs', $vpn_embed_qs);
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
$smarty->display('vpn-control.tpl');
