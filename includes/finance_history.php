<?php

function pm_finance_history_status_label($status)
{
    $status = strtolower(trim((string)$status));
    if ($status === 'paid') {
        return 'Completado';
    }
    if ($status === 'pending') {
        return 'Pendiente';
    }
    if ($status === 'expired') {
        return 'Expirado';
    }
    if ($status === 'failed') {
        return 'Fallido';
    }
    if ($status === 'cancelled') {
        return 'Cancelado';
    }

    return ucfirst($status);
}

function pm_finance_history_status_class($status)
{
    $status = strtolower(trim((string)$status));
    if ($status === 'paid') {
        return 'success';
    }
    if ($status === 'pending') {
        return 'pending';
    }
    if ($status === 'expired') {
        return 'expired';
    }
    if ($status === 'failed') {
        return 'failed';
    }
    if ($status === 'cancelled') {
        return 'cancelled';
    }

    return 'muted';
}

function pm_finance_history_try_json($value)
{
    if (!is_string($value) || trim($value) === '') {
        return array();
    }
    $decoded = json_decode($value, true);
    return is_array($decoded) ? $decoded : array();
}

function pm_finance_history_contexts($raw)
{
    $contexts = array();
    $root = pm_finance_history_try_json((string)$raw);
    if (!empty($root)) {
        $contexts[] = $root;
        foreach (array('Data', 'data', 'additionalData', 'additional_data', 'remitente', 'Remitente') as $key) {
            if (!isset($root[$key])) {
                continue;
            }
            if (is_array($root[$key])) {
                $contexts[] = $root[$key];
                continue;
            }
            $nested = pm_finance_history_try_json((string)$root[$key]);
            if (!empty($nested)) {
                $contexts[] = $nested;
            }
        }
        if (isset($root['data']) && is_array($root['data'])) {
            foreach (array('remitente', 'Remitente') as $key) {
                if (isset($root['data'][$key]) && is_array($root['data'][$key])) {
                    $contexts[] = $root['data'][$key];
                }
            }
        }
    }

    return $contexts;
}

function pm_finance_history_path($data, $path, $default = '')
{
    if (!is_array($data) || trim((string)$path) === '') {
        return $default;
    }
    $parts = explode('.', (string)$path);
    $cursor = $data;
    foreach ($parts as $part) {
        if (!is_array($cursor) || !array_key_exists($part, $cursor)) {
            return $default;
        }
        $cursor = $cursor[$part];
    }
    return $cursor;
}

function pm_finance_history_first($contexts, $paths, $default = '')
{
    foreach ((array)$contexts as $ctx) {
        if (!is_array($ctx)) {
            continue;
        }
        foreach ((array)$paths as $path) {
            $value = pm_finance_history_path($ctx, $path, '__pm_missing__');
            if ($value !== '__pm_missing__' && $value !== null && trim((string)$value) !== '') {
                return $value;
            }
        }
    }

    return $default;
}

function pm_finance_history_number($value, $decimals = 2)
{
    if ($value === '' || $value === null) {
        return '';
    }
    return number_format((float)$value, $decimals);
}

function pm_finance_history_money_clean($value, $decimals = null)
{
    if ($value === '' || $value === null) {
        return '';
    }

    if (is_string($value)) {
        $clean = trim((string)$value);
        $clean = preg_replace('/\s*Bs\.?\s*$/iu', '', $clean);
        $clean = str_replace(',', '', $clean);
        if (is_numeric($clean)) {
            $value = (float)$clean;
        } else {
            return trim((string)$clean);
        }
    }

    if ($decimals === null) {
        $decimals = (((float)$value - floor((float)$value)) == 0.0) ? 0 : 3;
    }

    return pm_finance_history_number((float)$value, (int)$decimals);
}

function pm_finance_history_type_human($value, $fallback = 'Qr')
{
    $value = trim((string)$value);
    if ($value === '') {
        return $fallback;
    }

    $normalized = strtolower($value);
    if ($normalized === '050' || $normalized === '50') {
        return 'Qr';
    }
    if ($normalized === 'api') {
        return 'API';
    }
    if ($normalized === 'qr') {
        return 'Qr';
    }

    return $value;
}

function pm_finance_history_relative($datetime)
{
    $datetime = trim((string)$datetime);
    if ($datetime === '' || $datetime === '0000-00-00 00:00:00') {
        return '';
    }
    $timestamp = strtotime($datetime);
    if ($timestamp <= 0) {
        return '';
    }

    $diff = time() - $timestamp;
    if ($diff < 0) {
        $diff = 0;
    }
    if ($diff < 60) {
        return 'Creado hace segundos';
    }
    if ($diff < 3600) {
        $minutes = (int)floor($diff / 60);
        return 'Creado hace ' . $minutes . ' minuto' . ($minutes === 1 ? '' : 's');
    }
    if ($diff < 86400) {
        $hours = (int)floor($diff / 3600);
        return 'Creado hace ' . $hours . ' hora' . ($hours === 1 ? '' : 's');
    }
    $days = (int)floor($diff / 86400);
    return 'Creado hace ' . $days . ' día' . ($days === 1 ? '' : 's');
}

function pm_finance_history_relative_paid($datetime)
{
    $datetime = trim((string)$datetime);
    if ($datetime === '' || $datetime === '0000-00-00 00:00:00') {
        return '';
    }
    $timestamp = strtotime($datetime);
    if ($timestamp <= 0) {
        return '';
    }

    $diff = time() - $timestamp;
    if ($diff < 0) {
        $diff = 0;
    }
    if ($diff < 60) {
        return 'Pagado hace segundos';
    }
    if ($diff < 3600) {
        $minutes = (int)floor($diff / 60);
        return 'Pagado hace ' . $minutes . ' minuto' . ($minutes === 1 ? '' : 's');
    }
    if ($diff < 86400) {
        $hours = (int)floor($diff / 3600);
        return 'Pagado hace ' . $hours . ' hora' . ($hours === 1 ? '' : 's');
    }
    $days = (int)floor($diff / 86400);
    return 'Pagado hace ' . $days . ' día' . ($days === 1 ? '' : 's');
}

function pm_finance_history_format_date($datetime)
{
    $datetime = trim((string)$datetime);
    if ($datetime === '' || $datetime === '0000-00-00 00:00:00') {
        return '-';
    }
    $timestamp = strtotime($datetime);
    if ($timestamp <= 0) {
        return '-';
    }
    return date('d-m-Y H:i:s', $timestamp);
}

function pm_finance_history_modal_rows($detail)
{
    return array(
        array('label' => 'BCP ID', 'value' => $detail['bcp_id']),
        array('label' => 'Tipo', 'value' => $detail['modal_type']),
        array('label' => 'Monto', 'value' => $detail['amount_bs'] !== '' ? $detail['amount_bs'] . ' Bs.' : '-'),
        array('label' => 'Detalle', 'value' => $detail['detail_full']),
        array('label' => 'Comisión', 'value' => $detail['commission_bs'] !== '' ? $detail['commission_bs'] . ' Bs.' : '-'),
        array('label' => 'Nombre del remitente', 'value' => $detail['payer_name']),
        array('label' => 'Banco del remitente', 'value' => $detail['payer_bank']),
        array('label' => 'Cuenta del remitente', 'value' => $detail['payer_account']),
        array('label' => 'Documentación del remitente', 'value' => $detail['payer_document']),
        array('label' => 'Fecha del pago', 'value' => $detail['paid_at_full']),
        array('label' => 'Estado', 'value' => $detail['status_label'])
    );
}

function pm_finance_history_veripagos_method($db)
{
    $methods = programmit_finance_list_methods($db, false);
    foreach ($methods as $method) {
        $providerKey = strtolower(trim((string)$method['provider_key']));
        $methodKey = strtolower(trim((string)$method['method_key']));
        $settings = isset($method['settings']) && is_array($method['settings']) ? $method['settings'] : array();
        $qrProvider = strtolower(trim((string)programmit_finance_setting_value($settings, 'qr_provider', '')));

        if ($providerKey !== 'veripagos_qr' && $methodKey !== 'veripagos' && $qrProvider !== 'veripagos') {
            continue;
        }

        $username = trim((string)programmit_finance_setting_value($settings, 'vp_dashboard_user', ''));
        $password = trim((string)programmit_finance_setting_value($settings, 'vp_dashboard_password', ''));
        if ($username === '' || $password === '') {
            $username = trim((string)programmit_finance_setting_value($settings, 'dashboard_user', ''));
            $password = trim((string)programmit_finance_setting_value($settings, 'dashboard_password', ''));
        }
        if ($username === '' || $password === '') {
            $username = trim((string)programmit_finance_setting_value($settings, 'api_user', ''));
            $password = trim((string)programmit_finance_setting_value($settings, 'api_password', ''));
        }
        if ($username !== '' && $password !== '') {
            return array(
                'username' => $username,
                'password' => $password
            );
        }
    }

    return array();
}

function pm_finance_history_cache_dir()
{
    $dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'cache';
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    return $dir;
}

function pm_finance_history_veripagos_cache_path($username)
{
    return pm_finance_history_cache_dir() . DIRECTORY_SEPARATOR . 'veripagos_movements_' . md5((string)$username) . '.json';
}

function pm_finance_history_veripagos_cache_read($username)
{
    $path = pm_finance_history_veripagos_cache_path($username);
    if (!is_file($path)) {
        return array('rows' => array(), 'fetched_at' => 0);
    }
    $decoded = json_decode((string)@file_get_contents($path), true);
    if (!is_array($decoded)) {
        return array('rows' => array(), 'fetched_at' => 0);
    }
    return array(
        'rows' => (isset($decoded['rows']) && is_array($decoded['rows'])) ? $decoded['rows'] : array(),
        'fetched_at' => isset($decoded['fetched_at']) ? (int)$decoded['fetched_at'] : 0
    );
}

function pm_finance_history_veripagos_http($url, $postFields = null, $headers = array(), &$cookies = array(), $cookieJar = '')
{
    if (!function_exists('curl_init')) {
        return array('ok' => false, 'body' => '', 'error' => 'curl_missing');
    }

    $ch = curl_init($url);
    $httpHeaders = array_merge(array(
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        'Accept-Language: es-ES,es;q=0.9,en;q=0.8'
    ), $headers);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 12);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders);
    curl_setopt($ch, CURLOPT_HEADER, true);

    $cookieJar = trim((string)$cookieJar);
    if ($cookieJar !== '') {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieJar);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieJar);
    }

    if (!empty($cookies)) {
        $cookiePairs = array();
        foreach ($cookies as $name => $value) {
            $cookiePairs[] = $name . '=' . $value;
        }
        if (!empty($cookiePairs)) {
            curl_setopt($ch, CURLOPT_COOKIE, implode('; ', $cookiePairs));
        }
    }

    if ($postFields !== null) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($postFields) ? http_build_query($postFields) : (string)$postFields);
    }

    $response = curl_exec($ch);
    $error = curl_error($ch);
    $headerSize = (int)curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false) {
        return array('ok' => false, 'body' => '', 'status' => $status, 'error' => $error);
    }

    $headerRaw = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    if (preg_match_all('/^Set-Cookie:\s*([^=]+)=([^;]*)/mi', $headerRaw, $cookieMatches, PREG_SET_ORDER)) {
        foreach ($cookieMatches as $match) {
            $cookies[$match[1]] = $match[2];
        }
    }

    return array(
        'ok' => ($status >= 200 && $status < 400),
        'status' => $status,
        'body' => $body,
        'error' => $error
    );
}

function pm_finance_history_veripagos_parse_movements($html)
{
    $html = (string)$html;
    if (trim($html) === '') {
        return array();
    }
    $rows = array();

    if (!preg_match_all('/<tr>\s*(.*?)<\/tr>/si', $html, $trMatches)) {
        return $rows;
    }

    foreach ($trMatches[1] as $trHtml) {
        if (!preg_match_all('/<td\b[^>]*>(.*?)<\/td>/si', $trHtml, $tdMatches) || count($tdMatches[1]) < 8) {
            continue;
        }

        $cells = array();
        foreach ($tdMatches[1] as $cellHtml) {
            $text = html_entity_decode(strip_tags(str_replace(array('<br>', '<br/>', '<br />'), "\n", $cellHtml)), ENT_QUOTES, 'UTF-8');
            $text = preg_replace('/[ \t]+/u', ' ', $text);
            $text = preg_replace('/\n\s+/u', "\n", $text);
            $cells[] = trim((string)$text);
        }

        $id = trim((string)$cells[0]);
        if ($id === '' || !preg_match('/^\d+$/', $id)) {
            continue;
        }

        if (stripos((string)$cells[4], 'VP:') === false) {
            continue;
        }

        $modalData = array();
        $onclick = '';
        if (preg_match('/onclick="([^"]*abrirModalInfo2\([^"]*)"/si', $trHtml, $onMatch)) {
            $onclick = html_entity_decode($onMatch[1], ENT_QUOTES, 'UTF-8');
        }
        if ($onclick !== '' && preg_match("/abrirModalInfo2\\((\\{.*?\\}),\\s*'([^']*)'\\s*,\\s*'([^']*)'\\s*,\\s*'([^']*)'\\)/si", $onclick, $m)) {
            $modalData = json_decode($m[1], true);
            if (!is_array($modalData)) {
                $modalData = array();
            }
        }

        $rows[$id] = array(
            'id' => $id,
            'bcp_id' => $cells[1],
            'amount_bs' => $cells[2],
            'commission_bs' => $cells[3],
            'detail' => $cells[4],
            'type' => $cells[5],
            'status' => $cells[6],
            'date' => $cells[7],
            'modal' => $modalData
        );
    }

    return $rows;
}

function pm_finance_history_veripagos_pages($html)
{
    $html = (string)$html;
    $pages = array(1);
    if (preg_match_all('/href="[^"]*movimientos\?page=(\d+)"/i', $html, $matches)) {
        foreach ($matches[1] as $page) {
            $page = (int)$page;
            if ($page > 1) {
                $pages[] = $page;
            }
        }
    }
    $pages = array_values(array_unique($pages));
    sort($pages);
    return array_slice($pages, 0, 5);
}

function pm_finance_history_veripagos_movements($username, $password)
{
    $username = trim((string)$username);
    $password = trim((string)$password);
    if ($username === '' || $password === '') {
        return array();
    }

    $cachePath = pm_finance_history_veripagos_cache_path($username);
    $cache = pm_finance_history_veripagos_cache_read($username);
    if (!empty($cache['rows']) && (time() - (int)$cache['fetched_at']) < 600) {
        return $cache['rows'];
    }

    $cookies = array();
    $cookieJar = tempnam(pm_finance_history_cache_dir(), 'vp_cookie_');
    $loginPage = pm_finance_history_veripagos_http('https://veripagos.com/login', null, array(), $cookies, (string)$cookieJar);
    if (empty($loginPage['ok'])) {
        if (is_string($cookieJar) && $cookieJar !== '' && is_file($cookieJar)) {
            @unlink($cookieJar);
        }
        return array();
    }

    if (!preg_match('/name="_token"\s+value="([^"]+)"/', (string)$loginPage['body'], $csrfMatch)) {
        if (is_string($cookieJar) && $cookieJar !== '' && is_file($cookieJar)) {
            @unlink($cookieJar);
        }
        return array();
    }

    $loginRequest = pm_finance_history_veripagos_http(
        'https://veripagos.com/login',
        array(
            '_token' => $csrfMatch[1],
            'username' => $username,
            'password' => $password
        ),
        array(
            'Content-Type: application/x-www-form-urlencoded',
            'Origin: https://veripagos.com',
            'Referer: https://veripagos.com/login'
        ),
        $cookies,
        (string)$cookieJar
    );

    if (empty($loginRequest['ok'])) {
        if (is_string($cookieJar) && $cookieJar !== '' && is_file($cookieJar)) {
            @unlink($cookieJar);
        }
        return array();
    }

    $movementsPage = pm_finance_history_veripagos_http('https://veripagos.com/movimientos', null, array(
        'Referer: https://veripagos.com/dashboard'
    ), $cookies, (string)$cookieJar);
    if (is_string($cookieJar) && $cookieJar !== '' && is_file($cookieJar)) {
        // unlink after all page requests
    }
    if (empty($movementsPage['ok'])) {
        if (is_string($cookieJar) && $cookieJar !== '' && is_file($cookieJar)) {
            @unlink($cookieJar);
        }
        return !empty($cache['rows']) ? $cache['rows'] : array();
    }

    $pages = pm_finance_history_veripagos_pages((string)$movementsPage['body']);
    $rows = pm_finance_history_veripagos_parse_movements((string)$movementsPage['body']);
    foreach ($pages as $page) {
        if ($page <= 1) {
            continue;
        }
        $pageRequest = pm_finance_history_veripagos_http(
            'https://veripagos.com/movimientos?page=' . (int)$page,
            null,
            array('Referer: https://veripagos.com/movimientos'),
            $cookies,
            (string)$cookieJar
        );
        if (empty($pageRequest['ok'])) {
            continue;
        }
        $rows = array_replace($rows, pm_finance_history_veripagos_parse_movements((string)$pageRequest['body']));
    }
    if (is_string($cookieJar) && $cookieJar !== '' && is_file($cookieJar)) {
        @unlink($cookieJar);
    }
    if (!empty($rows)) {
        @file_put_contents($cachePath, json_encode(array(
            'fetched_at' => time(),
            'rows' => $rows
        ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        return $rows;
    }

    return !empty($cache['rows']) ? $cache['rows'] : array();
}

function pm_finance_history_selected_range_days($query)
{
    $allowedRangeDays = array(7, 30, 90);
    $selectedRangeDays = isset($query['range']) ? (int)$query['range'] : 30;
    if (!in_array($selectedRangeDays, $allowedRangeDays, true)) {
        $selectedRangeDays = 30;
    }

    return $selectedRangeDays;
}

function pm_finance_history_range_context($selectedRangeDays)
{
    $selectedRangeDays = (int)$selectedRangeDays;
    if ($selectedRangeDays <= 0) {
        $selectedRangeDays = 30;
    }

    $rangeStartTs = strtotime(date('Y-m-d 00:00:00', strtotime('-' . ($selectedRangeDays - 1) . ' days')));
    $rangeEndTs = strtotime(date('Y-m-d 23:59:59'));

    return array(
        'days' => $selectedRangeDays,
        'start_ts' => $rangeStartTs,
        'end_ts' => $rangeEndTs,
        'start_sql' => date('Y-m-d H:i:s', $rangeStartTs),
        'label' => 'Últimos ' . $selectedRangeDays . ' días (' . date('d/m/Y', $rangeStartTs) . ' - ' . date('d/m/Y', $rangeEndTs) . ')'
    );
}

function pm_finance_history_list_recharges($db, $userId, $rangeStartSql, $limit = 250)
{
    $userId = (int)$userId;
    $limit = (int)$limit;
    if ($userId <= 0) {
        return array();
    }
    if ($limit <= 0) {
        $limit = 250;
    }
    if ($limit > 500) {
        $limit = 500;
    }

    $qry = $db->sql_query("SELECT *
        FROM finance_recharges
        WHERE user_id='" . $db->SanitizeForSQL($userId) . "'
          AND created_at>='" . $db->SanitizeForSQL($rangeStartSql) . "'
          AND status IN ('paid','pending')
          AND (
                provider_txn_id<>''
                OR provider_response IS NOT NULL
                OR LOWER(method_name) LIKE '%qr%'
              )
        ORDER BY id DESC
        LIMIT " . $db->SanitizeForSQL($limit));

    $rows = array();
    while ($row = $db->sql_fetchrow($qry)) {
        if ($row) {
            $rows[] = $row;
        }
    }

    return $rows;
}

function pm_finance_history_merge_veripagos_view($view, $vp, $row)
{
    if (trim((string)$vp['bcp_id']) !== '') {
        $view['bcp_id'] = trim((string)$vp['bcp_id']);
    }
    if (trim((string)$vp['amount_bs']) !== '') {
        $view['amount_bs'] = pm_finance_history_money_clean($vp['amount_bs']);
    }
    if (trim((string)$vp['commission_bs']) !== '') {
        $view['commission_bs'] = pm_finance_history_money_clean($vp['commission_bs']);
    }
    if (trim((string)$vp['status']) !== '') {
        $view['status_label'] = trim((string)$vp['status']);
        $view['status_class'] = pm_finance_history_status_class(trim((string)$row['status']));
    }

    if (empty($vp['modal']) || !is_array($vp['modal'])) {
        return $view;
    }

    $modal = $vp['modal'];
    $detailValue = trim((string)($modal['detalle'] ?? ''));
    if ($detailValue === '') {
        $detailValue = $view['detail_header'] . ' ' . $view['detail_reference'];
    }

    $detailRows = array(
        array('label' => 'BCP ID', 'value' => trim((string)($modal['bcp_id'] ?? $view['bcp_id'] ?: '-'))),
        array('label' => 'Tipo', 'value' => pm_finance_history_type_human((string)($modal['tipo'] ?? ''), 'Qr')),
        array('label' => 'Monto', 'value' => trim((string)($vp['amount_bs'] ?: '-'))),
        array('label' => 'Detalle', 'value' => $detailValue),
        array('label' => 'Comisión', 'value' => trim((string)($vp['commission_bs'] ?: '-'))),
        array('label' => 'Nombre del remitente', 'value' => trim((string)($modal['receiver_name'] ?? $modal['remitente']['nombre'] ?? '-'))),
        array('label' => 'Banco del remitente', 'value' => trim((string)($modal['receiver_bank'] ?? $modal['remitente']['banco'] ?? '-'))),
        array('label' => 'Cuenta del remitente', 'value' => trim((string)($modal['receiver_account'] ?? $modal['remitente']['cuenta'] ?? '-'))),
        array('label' => 'Documentación del remitente', 'value' => trim((string)($modal['receiver_document'] ?? $modal['remitente']['documento'] ?? '-'))),
        array('label' => 'Fecha del pago', 'value' => trim((string)($modal['fecha_de_pago'] ?? $view['paid_at']))),
        array('label' => 'Estado', 'value' => trim((string)($vp['status'] ?: $view['status_label'])))
    );

    $view['info_rows'] = $detailRows;
    $view['info_rows_json'] = htmlspecialchars(json_encode($detailRows, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8');

    return $view;
}

function pm_finance_history_is_legacy_row($view, $hasVeripagosMatch)
{
    return (
        !$hasVeripagosMatch
        && trim((string)$view['bcp_id']) === '-'
        && (float)str_replace(',', '', (string)$view['commission_bs']) <= 0.0
    );
}

function pm_finance_history_row($row, $baseUrl)
{
    $contexts = pm_finance_history_contexts((string)$row['provider_response']);
    $status = strtolower(trim((string)$row['status']));
    $statusLabel = pm_finance_history_status_label($status);
    $statusClass = pm_finance_history_status_class($status);

    $providerTxnId = trim((string)$row['provider_txn_id']);
    if ($providerTxnId === '') {
        $providerTxnId = trim((string)pm_finance_history_first($contexts, array(
            'movimiento_id',
            'MovimientoId',
            'movimientoId',
            'id',
            'Data.movimiento_id'
        ), ''));
    }

    $bcpId = trim((string)pm_finance_history_first($contexts, array(
        'bcp_id',
        'BCP_ID',
        'bcpId',
        'Data.bcp_id',
        'Data.BCP_ID',
        'transaccion_id'
    ), ''));

    $amountBs = pm_finance_history_first($contexts, array(
        'monto',
        'monto_original',
        'Data.monto',
        'data.monto'
    ), '');
    if ($amountBs === '') {
        $amountBs = (string)$row['total_bob'];
    }

    $commissionBs = pm_finance_history_first($contexts, array(
        'comision',
        'Data.comision',
        'data.comision'
    ), '');
    if ($commissionBs === '') {
        $commissionBs = '0';
    }

    $detailFull = trim((string)pm_finance_history_first($contexts, array(
        'detalle',
        'Detail',
        'detalle_qr',
        'Gloss',
        'gloss',
        'Data.detalle'
    ), ''));
    if ($detailFull === '') {
        $detailFull = 'VP: ' . ($providerTxnId !== '' ? $providerTxnId : (string)$row['id']) . ' Recarga ' . (string)$row['recharge_ref'];
    }

    $payerName = trim((string)pm_finance_history_first($contexts, array(
        'remitente.nombre',
        'data.remitente.nombre',
        'receiver_name',
        'sender_name'
    ), '-'));
    $payerBank = trim((string)pm_finance_history_first($contexts, array(
        'remitente.banco',
        'data.remitente.banco',
        'receiver_bank',
        'sender_bank'
    ), '-'));
    $payerAccount = trim((string)pm_finance_history_first($contexts, array(
        'remitente.cuenta',
        'data.remitente.cuenta',
        'receiver_account',
        'sender_account'
    ), '-'));
    $payerDocument = trim((string)pm_finance_history_first($contexts, array(
        'remitente.documento',
        'data.remitente.documento',
        'receiver_document',
        'sender_document'
    ), '-'));

    $singleUse = (int)pm_finance_history_first($contexts, array(
        'uso_unico',
        'singleUse',
        'Data.singleUse',
        'data.singleUse'
    ), 1);

    $typeLabel = 'API';
    $modalType = (stripos((string)$row['method_name'], 'qr') !== false) ? 'Qr' : 'Recarga';
    if (stripos((string)$row['method_name'], 'manual') !== false) {
        $typeLabel = 'Manual';
        $modalType = 'Manual';
    }

    $createdAt = pm_finance_history_format_date((string)$row['created_at']);
    $paidAtFull = pm_finance_history_first($contexts, array(
        'fecha_de_pago',
        'data.fecha_de_pago'
    ), '');
    if ($paidAtFull === '') {
        $paidAtFull = (string)$row['paid_at'];
    }
    $paidAt = pm_finance_history_format_date($paidAtFull);

    $detailRows = pm_finance_history_modal_rows(array(
        'bcp_id' => ($bcpId !== '' ? $bcpId : '-'),
        'modal_type' => $modalType,
        'amount_bs' => pm_finance_history_money_clean($amountBs, 0),
        'detail_full' => $detailFull,
        'commission_bs' => pm_finance_history_money_clean($commissionBs, 3),
        'payer_name' => $payerName !== '' ? $payerName : '-',
        'payer_bank' => $payerBank !== '' ? $payerBank : '-',
        'payer_account' => $payerAccount !== '' ? $payerAccount : '-',
        'payer_document' => $payerDocument !== '' ? $payerDocument : '-',
        'paid_at_full' => $paidAt !== '' ? $paidAt : '-',
        'reference' => (string)$row['recharge_ref'],
        'status_label' => $statusLabel
    ));

    return array(
        'provider_txn_id' => ($providerTxnId !== '' ? $providerTxnId : (string)$row['id']),
        'bcp_id' => ($bcpId !== '' ? $bcpId : '-'),
        'amount_bs' => pm_finance_history_money_clean($amountBs, 0),
        'commission_bs' => pm_finance_history_money_clean($commissionBs, 3),
        'detail_header' => 'VP: ' . (($providerTxnId !== '' ? $providerTxnId : (string)$row['id'])) . ' Recarga',
        'detail_reference' => (string)$row['recharge_ref'],
        'type_label' => $typeLabel,
        'show_single_use' => ($singleUse === 1),
        'status_label' => $statusLabel,
        'status_class' => $statusClass,
        'created_at' => $createdAt,
        'paid_at' => $status === 'paid' ? $paidAt : '-',
        'info_available' => ($status === 'paid'),
        'info_rows' => $detailRows,
        'info_rows_json' => htmlspecialchars(json_encode($detailRows, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8'),
        'checkout_url' => rtrim((string)$baseUrl, '/') . '/index.php?p=finance-checkout&id=' . (int)$row['id']
    );
}

function pm_finance_history_build_view_model($db, $userId, $query = array())
{
    programmit_finance_ensure_tables($db);

    $range = pm_finance_history_range_context(pm_finance_history_selected_range_days((array)$query));
    $veripagosMethod = pm_finance_history_veripagos_method($db);
    $veripagosRows = array();
    if (!empty($veripagosMethod)) {
        $veripagosRows = pm_finance_history_veripagos_movements($veripagosMethod['username'], $veripagosMethod['password']);
    }

    $recharges = pm_finance_history_list_recharges($db, (int)$userId, $range['start_sql'], 250);
    $rows = array();
    $paidCount = 0;
    $paidAmountBs = 0.0;

    foreach ($recharges as $row) {
        $status = strtolower(trim((string)$row['status']));
        $view = pm_finance_history_row($row, $db->base_url());
        $hasVeripagosMatch = false;

        if (!empty($veripagosRows) && isset($veripagosRows[(string)$view['provider_txn_id']])) {
            $hasVeripagosMatch = true;
            $view = pm_finance_history_merge_veripagos_view($view, $veripagosRows[(string)$view['provider_txn_id']], $row);
        }

        if (pm_finance_history_is_legacy_row($view, $hasVeripagosMatch)) {
            continue;
        }

        if ($status === 'paid') {
            $paidCount++;
            $paidAmountBs += (float)$row['total_bob'];
        }

        $rows[] = $view;
    }

    $historyBaseUrl = rtrim((string)$db->base_url(), '/') . '/index.php?p=finance-history';

    return array(
        'finance_history_rows' => $rows,
        'finance_history_paid_total' => $paidCount,
        'finance_history_paid_bob' => number_format($paidAmountBs, 2),
        'finance_history_range' => $range['label'],
        'finance_history_range_days' => $range['days'],
        'finance_history_range_url_7' => $historyBaseUrl . '&range=7',
        'finance_history_range_url_30' => $historyBaseUrl . '&range=30',
        'finance_history_range_url_90' => $historyBaseUrl . '&range=90'
    );
}
