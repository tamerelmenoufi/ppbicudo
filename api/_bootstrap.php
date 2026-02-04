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
    $contentType = $_SERVER['CONTENT_TYPE'] ?: $_SERVER['HTTP_CONTENT_TYPE'] ?: '';
    if ($raw && stripos($contentType, 'application/json') !== false) {
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            $data = $decoded;
        }
    }

    // POST should override JSON body if both are present (common with form posts).
    if (!empty($_POST)) {
        $data = array_merge($data, $_POST);
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

