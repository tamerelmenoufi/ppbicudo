<?php

include("{$_SERVER['DOCUMENT_ROOT']}/lib/includes.php");

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/config.php';

function api_json_response($data, int $statusCode = 200): void {
    http_response_code($statusCode);
    echo json_encode(
        $data,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit;
}

function api_error(string $message, int $statusCode = 400, array $extra = []): void {
    api_json_response(array_merge(['ok' => false, 'error' => $message], $extra), $statusCode);
}

function api_read_input(): array {
    $data = [];

    $raw = file_get_contents('php://input');
    $contentType = $_SERVER['CONTENT_TYPE'] ?? ($_SERVER['HTTP_CONTENT_TYPE'] ?? '');

    // 1) JSON body (by header or by sniffing).
    $rawTrim = is_string($raw) ? ltrim($raw) : '';
    $looksJson = $rawTrim !== '' && ($rawTrim[0] === '{' || $rawTrim[0] === '[');
    if ($raw && (stripos($contentType, 'application/json') !== false || $looksJson)) {
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            $data = $decoded;
        }
    }

    // POST should override JSON body if both are present (common with form posts).
    if (!empty($_POST)) {
        $data = array_merge($data, $_POST);
    }

    // 2) Tolerate clients that send form bodies with a wrong/missing Content-Type.
    // This helps with some Postman/cURL exports where `--form` is used but headers are edited manually.
    if (!$data && $raw) {
        $tmp = [];
        parse_str($raw, $tmp);
        if (is_array($tmp) && $tmp) {
            $data = $tmp;
        }
    }

    // 3) Minimal multipart/form-data parsing fallback (fields only; no files).
    if (!$data && $raw && stripos($raw, 'Content-Disposition: form-data;') !== false) {
        $firstLine = strtok($raw, "\r\n");
        if (is_string($firstLine) && str_starts_with($firstLine, '--')) {
            $boundary = substr($firstLine, 2);
            if ($boundary !== '') {
                $parts = preg_split('/\R--' . preg_quote($boundary, '/') . '(?:--)?\R/', $raw);
                if (is_array($parts)) {
                    foreach ($parts as $part) {
                        if (!is_string($part) || trim($part) === '' || stripos($part, 'Content-Disposition:') === false) {
                            continue;
                        }
                        [$headers, $body] = array_pad(preg_split("/\R\R/", $part, 2), 2, '');
                        if (!is_string($headers) || !is_string($body)) {
                            continue;
                        }
                        if (preg_match('/name="([^"]+)"/', $headers, $m)) {
                            $name = $m[1];
                            $value = rtrim($body, "\r\n");
                            if ($name !== '') {
                                $data[$name] = $value;
                            }
                        }
                    }
                }
            }
        }
    }

    return $data;
}

function api_require_post(): void {
    $method = strtoupper($_SERVER['REQUEST_METHOD'] ?: 'GET');
    if ($method !== 'POST') {
        api_error('Método não permitido. Use POST.', 405);
    }
}

function api_require_auth(array $input): void {
    if (!defined('PPBICUDO_API_CREDENCIAL') || PPBICUDO_API_CREDENCIAL === '') {
        api_error('API credencial não configurada. Defina PPBICUDO_API_CREDENCIAL.', 500);
    }

    $credencial = (string)($input['credencial'] ?: '');
    if ($credencial === '' || !hash_equals(PPBICUDO_API_CREDENCIAL, $credencial)) {
        api_error('Credencial inválida.', 401);
    }
}

function api_stmt_bind(mysqli_stmt $stmt, string $types, array $params): void {
    $refs = [];
    foreach ($params as $i => $v) {
        $refs[$i] = &$params[$i];
    }
    mysqli_stmt_bind_param($stmt, $types, ...$refs);
}
