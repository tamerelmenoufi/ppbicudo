<?php

require_once __DIR__ . '/../_bootstrap.php';

api_require_post();
$input = api_read_input();
api_require_auth($input);

$mes = isset($input['mes']) ? (int)$input['mes'] : 0;
$ano = isset($input['ano']) ? (int)$input['ano'] : 0;

if ($mes < 1 || $mes > 12) {
    api_error('Parâmetro mes inválido.', 422);
}
if ($ano < 2000 || $ano > 2100) {
    api_error('Parâmetro ano inválido.', 422);
}

$start = sprintf('%04d-%02d-01', $ano, $mes);
$endDt = new DateTimeImmutable($start, new DateTimeZone('UTC'));
$end = $endDt->modify('+1 month')->format('Y-m-d');

$sql = "SELECT codigo, origem, nome, registros
        FROM relatorio_modelos
        WHERE data >= ? AND data < ?
        ORDER BY codigo DESC";

$stmt = mysqli_prepare($con, $sql);
if (!$stmt) {
    api_error('Erro ao preparar consulta de modelos.', 500);
}
api_stmt_bind($stmt, 'ss', [$start, $end]);

if (!mysqli_stmt_execute($stmt)) {
    api_error('Erro ao executar consulta de modelos.', 500);
}

$result = mysqli_stmt_get_result($stmt);
if (!$result) {
    api_error('Erro ao obter resultado de modelos.', 500);
}

$rows = [];
while ($d = mysqli_fetch_assoc($result)) {
    $registros = json_decode($d['registros'] ?? '[]', true);
    if (!is_array($registros)) {
        $registros = [];
    }

    $rows[] = [
        'codigo' => (int)$d['codigo'],
        'origem' => (int)$d['origem'],
        'nome' => $d['nome'],
        'registros' => array_values($registros),
    ];
}

api_json_response($rows);

