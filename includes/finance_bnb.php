<?php
/**
 * Cliente BNB QR Simple para integracion de recargas.
 *
 * Flujo soportado:
 * - obtener token
 * - generar QR
 * - consultar estado
 * - cancelar QR
 */
class ProgrammitFinanceBnbQrSimple
{
    private const BNB_TIMEZONE = 'America/La_Paz';

    private string $tokenUrl;
    private string $qrUrl;
    private string $statusUrl;
    private string $cancelUrl;
    private string $accountId;
    private string $authorizationId;
    private string $currency;
    private int $destinationAccountId;
    private ?string $cachedToken = null;
    private int $tokenExpiresAt = 0;

    public function __construct(array $config = array())
    {
        $this->tokenUrl = trim((string)($config['token_url'] ?? 'http://test.bnb.com.bo/ClientAuthentication.API/api/v1/auth/token'));
        $this->qrUrl = trim((string)($config['qr_url'] ?? 'http://test.bnb.com.bo/QRSimple.API/api/v1/main/getQRWithImageAsync'));
        $this->statusUrl = trim((string)($config['status_url'] ?? 'http://test.bnb.com.bo/QRSimple.API/api/v1/main/getQRStatusAsync'));
        $this->cancelUrl = trim((string)($config['cancel_url'] ?? 'http://test.bnb.com.bo/QRSimple.API/api/v1/main/CancelQRByIdAsync'));
        $this->accountId = trim((string)($config['account_id'] ?? ''));
        $this->authorizationId = trim((string)($config['authorization_id'] ?? ''));
        $this->currency = strtoupper(trim((string)($config['currency'] ?? 'BOB')));
        if ($this->currency === '') {
            $this->currency = 'BOB';
        }
        $this->destinationAccountId = max(1, (int)($config['destination_account_id'] ?? 1));
    }

    /**
     * @return array{success:bool,data?:array<string,mixed>,error?:string,raw?:array<string,mixed>}
     */
    public function generarQR(
        float $montoBs,
        array $dataExtra = array(),
        int $expiryMinutes = 15,
        ?string $detalle = null,
        bool $singleUse = true
    ): array {
        $montoBs = round(max(0.01, $montoBs), 2);
        $expiryMinutes = max(1, min(1440, $expiryMinutes));
        $expirationMeta = $this->buildExpirationMeta($expiryMinutes);
        $expirationDate = $expirationMeta['payload_date'];

        $payload = array(
            'currency' => $this->currency,
            'gloss' => trim((string)($detalle ?? 'Recarga QR Bolivia')),
            'amount' => $montoBs,
            'singleUse' => $singleUse,
            'expirationDate' => $expirationDate,
            'destinationAccountId' => (string)$this->destinationAccountId,
        );

        if (!empty($dataExtra)) {
            $payload['additionalData'] = json_encode($dataExtra, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        $request = $this->requestAuthenticatedJson($this->qrUrl, $payload);
        if (!$request['success']) {
            return array(
                'success' => false,
                'error' => (string)($request['error'] ?? 'No se pudo generar QR en BNB'),
            );
        }

        $responseData = is_array($request['data'] ?? null) ? $request['data'] : array();
        if (!$this->isSuccessResponse($responseData)) {
            return array(
                'success' => false,
                'error' => $this->extractErrorMessage($responseData, 'BNB rechazo la generacion del QR'),
                'raw' => $responseData,
            );
        }

        $qrId = (string)($responseData['id'] ?? '');
        $qrBase64 = $this->normalizeQrToBase64($responseData['qr'] ?? null);
        if ($qrId === '' || $qrBase64 === '') {
            return array(
                'success' => false,
                'error' => 'Respuesta incompleta de BNB al generar QR',
                'raw' => $responseData,
            );
        }

        return array(
            'success' => true,
            'data' => array(
                'movimiento_id' => $qrId,
                'qr' => $qrBase64,
                'qr_image' => 'data:image/png;base64,' . $qrBase64,
                'expiration_date' => $this->normalizeExpirationDate((string)($responseData['expirationDate'] ?? '')) ?: $expirationMeta['storage_value'],
            ),
            'raw' => $responseData,
        );
    }

    /**
     * @return array{success:bool,data?:array<string,mixed>,error?:string,raw?:array<string,mixed>}
     */
    public function verificarEstado(string $qrId): array
    {
        $qrId = trim($qrId);
        if ($qrId === '') {
            return array(
                'success' => false,
                'error' => 'qrId requerido',
            );
        }

        $request = $this->requestAuthenticatedJson($this->statusUrl, array(
            'qrId' => (int)$qrId,
        ));
        if (!$request['success']) {
            return array(
                'success' => false,
                'error' => (string)($request['error'] ?? 'No se pudo consultar estado QR BNB'),
            );
        }

        $responseData = is_array($request['data'] ?? null) ? $request['data'] : array();
        if (!$this->isSuccessResponse($responseData)) {
            return array(
                'success' => false,
                'error' => $this->extractErrorMessage($responseData, 'BNB no pudo verificar el pago'),
                'raw' => $responseData,
            );
        }

        $statusId = (int)($responseData['statusId'] ?? 0);
        $normalizedStatus = 'pendiente';
        if ($statusId === 2) {
            $normalizedStatus = 'completado';
        } elseif ($statusId === 3) {
            $normalizedStatus = 'expirado';
        } elseif ($statusId === 4) {
            $normalizedStatus = 'fallido';
        }

        return array(
            'success' => true,
            'data' => array(
                'movimiento_id' => (string)($responseData['id'] ?? $qrId),
                'estado' => $normalizedStatus,
                'status_id' => $statusId,
                'voucher_id' => (string)($responseData['voucherId'] ?? ''),
                'expiration_date' => $this->normalizeExpirationDate((string)($responseData['expirationDate'] ?? '')),
            ),
            'raw' => $responseData,
        );
    }

    /**
     * @return array{success:bool,error?:string}
     */
    public function cancelarQR(string $qrId): array
    {
        $qrId = trim($qrId);
        if ($qrId === '') {
            return array(
                'success' => false,
                'error' => 'qrId requerido',
            );
        }

        $request = $this->requestAuthenticatedJson($this->cancelUrl, array(
            'qrId' => (int)$qrId,
        ));
        if (!$request['success']) {
            return array(
                'success' => false,
                'error' => (string)($request['error'] ?? 'No se pudo cancelar QR BNB'),
            );
        }

        $responseData = is_array($request['data'] ?? null) ? $request['data'] : array();
        if (!$this->isSuccessResponse($responseData)) {
            return array(
                'success' => false,
                'error' => $this->extractErrorMessage($responseData, 'BNB no pudo cancelar el QR'),
            );
        }

        return array('success' => true);
    }

    /**
     * @return array{success:bool,token?:string,error?:string}
     */
    public function obtenerTokenPublico(): array
    {
        return $this->obtenerToken();
    }

    /**
     * @return array{success:bool,data?:array<string,mixed>,error?:string}
     */
    private function requestAuthenticatedJson(string $url, array $payload): array
    {
        $tokenResult = $this->obtenerToken();
        if (!$tokenResult['success']) {
            return array(
                'success' => false,
                'error' => (string)($tokenResult['error'] ?? 'No se pudo obtener token BNB'),
            );
        }

        $headers = array(
            'Authorization: Bearer ' . (string)$tokenResult['token'],
        );
        return $this->requestJson($url, $payload, $headers);
    }

    /**
     * @return array{success:bool,token?:string,error?:string}
     */
    private function obtenerToken(): array
    {
        if ($this->cachedToken !== null && $this->cachedToken !== '' && time() < $this->tokenExpiresAt) {
            return array(
                'success' => true,
                'token' => $this->cachedToken,
            );
        }

        if ($this->accountId === '' || $this->authorizationId === '') {
            return array(
                'success' => false,
                'error' => 'Credenciales BNB incompletas (accountId/authorizationId)',
            );
        }

        $candidatePayloads = array(
            array(
                'accountId' => $this->accountId,
                'authorizationId' => $this->authorizationId,
            ),
            array(
                'username' => $this->accountId,
                'password' => $this->authorizationId,
            ),
        );

        $lastError = 'Respuesta invalida en token BNB';
        foreach ($candidatePayloads as $payload) {
            $request = $this->requestJson($this->tokenUrl, $payload);
            if (!$request['success']) {
                $lastError = (string)($request['error'] ?? $lastError);
                continue;
            }
            $responseData = is_array($request['data'] ?? null) ? $request['data'] : array();
            $token = $this->extractTokenFromResponse($responseData);
            if ($token === '') {
                $lastError = $this->extractErrorMessage($responseData, $lastError);
                continue;
            }

            $this->cachedToken = $token;
            $this->tokenExpiresAt = $this->resolveTokenExpiry($token);
            return array(
                'success' => true,
                'token' => $token,
            );
        }

        return array(
            'success' => false,
            'error' => $lastError,
        );
    }

    /**
     * @return array{success:bool,data?:array<string,mixed>,error?:string}
     */
    private function requestJson(string $url, array $payload, array $extraHeaders = array()): array
    {
        $url = trim($url);
        if ($url === '') {
            return array(
                'success' => false,
                'error' => 'URL de integracion vacia',
            );
        }

        $ch = curl_init($url);
        if ($ch === false) {
            return array(
                'success' => false,
                'error' => 'No se pudo inicializar cURL',
            );
        }

        $headers = array_merge(array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Cache-Control: no-cache',
        ), $extraHeaders);

        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 40,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
        ));

        $raw = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($raw === false || $error !== '') {
            return array(
                'success' => false,
                'error' => 'Error de conexion con BNB: ' . ($error !== '' ? $error : 'sin respuesta'),
            );
        }

        $decoded = json_decode((string)$raw, true);
        if (!is_array($decoded)) {
            return array(
                'success' => false,
                'error' => 'Respuesta no JSON de BNB (HTTP ' . $httpCode . ')',
            );
        }

        return array(
            'success' => true,
            'data' => $decoded,
        );
    }

    private function extractTokenFromResponse(array $responseData): string
    {
        foreach (array('token', 'access_token', 'jwt') as $key) {
            $candidate = trim((string)($responseData[$key] ?? ''));
            if ($candidate !== '') {
                return $candidate;
            }
        }

        $message = trim((string)($responseData['message'] ?? ''));
        if ($message !== '' && $this->looksLikeJwt($message)) {
            return $message;
        }
        return '';
    }

    private function looksLikeJwt(string $value): bool
    {
        return substr_count($value, '.') === 2
            && (bool)preg_match('/^[A-Za-z0-9\-_\.]+$/', $value);
    }

    private function resolveTokenExpiry(string $token): int
    {
        $default = time() + (30 * 60);
        $parts = explode('.', $token);
        if (count($parts) < 2) {
            return $default;
        }

        $payload = $parts[1];
        $payload .= str_repeat('=', (4 - (strlen($payload) % 4)) % 4);
        $json = base64_decode(strtr($payload, '-_', '+/'), true);
        if ($json === false) {
            return $default;
        }

        $decoded = json_decode($json, true);
        if (!is_array($decoded) || empty($decoded['exp'])) {
            return $default;
        }

        $exp = (int)$decoded['exp'];
        if ($exp <= time() + 30) {
            return $default;
        }
        return max(time() + 60, $exp - 30);
    }

    private function isSuccessResponse(array $responseData): bool
    {
        if (!array_key_exists('success', $responseData)) {
            return true;
        }
        return (bool)$responseData['success'] === true;
    }

    private function extractErrorMessage(array $responseData, string $fallback): string
    {
        $message = trim((string)($responseData['message'] ?? ''));
        if ($message !== '') {
            return $message;
        }
        $mensaje = trim((string)($responseData['Mensaje'] ?? ''));
        if ($mensaje !== '') {
            return $mensaje;
        }
        return $fallback;
    }

    private function normalizeQrToBase64($qrField): string
    {
        if (is_string($qrField)) {
            $normalized = preg_replace('/\s+/', '', $qrField);
            $normalized = is_string($normalized) ? $normalized : '';
            if ($normalized !== '' && $this->looksLikeBase64($normalized)) {
                return $normalized;
            }
            if ($qrField !== '') {
                return base64_encode($qrField);
            }
            return '';
        }

        if (is_array($qrField) && !empty($qrField)) {
            $bytes = '';
            foreach ($qrField as $item) {
                if (!is_numeric($item)) {
                    continue;
                }
                $value = (int)$item;
                if ($value < 0) {
                    $value += 256;
                }
                $bytes .= chr($value & 0xFF);
            }
            if ($bytes !== '') {
                return base64_encode($bytes);
            }
        }

        return '';
    }

    private function looksLikeBase64(string $value): bool
    {
        if ($value === '' || (strlen($value) % 4) !== 0) {
            return false;
        }
        return (bool)preg_match('/^[A-Za-z0-9+\/=]+$/', $value);
    }

    /**
     * @return array{payload_date:string,storage_value:string}
     */
    private function buildExpirationMeta(int $expiryMinutes): array
    {
        $tz = new DateTimeZone(self::BNB_TIMEZONE);
        $target = new DateTimeImmutable('now', $tz);
        $target = $target->modify('+' . max(1, $expiryMinutes) . ' minutes');
        $payloadDate = $target->format('Y-m-d');
        $storageDate = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $payloadDate . ' 23:59:59', $tz);
        if (!$storageDate) {
            $storageDate = new DateTimeImmutable($payloadDate . ' 23:59:59', $tz);
        }

        return array(
            'payload_date' => $payloadDate,
            'storage_value' => $storageDate->format('Y-m-d\TH:i:sP'),
        );
    }

    private function normalizeExpirationDate(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        $tz = new DateTimeZone(self::BNB_TIMEZONE);
        $date = false;
        $formats = array(
            DateTimeInterface::ATOM,
            'Y-m-d\TH:i:s',
            'Y-m-d H:i:s',
            'Y-m-d',
        );

        foreach ($formats as $format) {
            if ($format === 'Y-m-d') {
                $candidate = DateTimeImmutable::createFromFormat($format . ' H:i:s', $value . ' 23:59:59', $tz);
            } else {
                $candidate = DateTimeImmutable::createFromFormat($format, $value, $tz);
            }
            if ($candidate instanceof DateTimeImmutable) {
                $date = $candidate;
                break;
            }
        }

        if (!$date) {
            try {
                $date = new DateTimeImmutable($value, $tz);
            } catch (Throwable $e) {
                return '';
            }
        }

        return $date->setTimezone($tz)->format('Y-m-d\TH:i:sP');
    }
}
