<?php

require_once __DIR__ . '/../_bootstrap.php';

api_require_post();
$input = api_read_input();
api_require_auth($input);

$registrosInput = $input['registros'] ?: null;
if (is_string($registrosInput)) {
    $decoded = json_decode($registrosInput, true);
    if (is_array($decoded)) {
        $registrosInput = $decoded;
    }
}

if (!is_array($registrosInput)) {
    api_error('Parâmetro registros deve ser um JSON array.', 422);
}

$codes = [];
foreach ($registrosInput as $v) {
    if (is_numeric($v)) {
        $codes[] = (int)$v;
    }
}
$codes = array_values(array_unique(array_filter($codes, fn($n) => $n > 0)));

if (!$codes) {
    api_json_response([]);
}

$placeholders = implode(',', array_fill(0, count($codes), '?'));

$sql = "SELECT
            codigo,
            origem,
            dataCriacao,
            codigoPedido,
            pedidoOrigem,
            tituloItem,
            frete,
            ValorPedidoXquantidade,
            CustoEnvio,
            CustoEnvioSeller,
            TarifaGatwayPagamento,
            TarifaMarketplace,
            PrecoCusto,
            Porcentagem,
            Conta,
            observacoes
        FROM relatorio
        WHERE codigo IN ($placeholders)
        ORDER BY FIELD(codigo, " . implode(',', $codes) . ")";

$stmt = mysqli_prepare($con, $sql);
if (!$stmt) {
    api_error('Erro ao preparar consulta do relatório.', 500);
}

$types = str_repeat('i', count($codes));
api_stmt_bind($stmt, $types, $codes);

if (!mysqli_stmt_execute($stmt)) {
    api_error('Erro ao executar consulta do relatório.', 500);
}

$result = mysqli_stmt_get_result($stmt);
if (!$result) {
    api_error('Erro ao obter resultado do relatório.', 500);
}

$rows = [];
while ($d = mysqli_fetch_assoc($result)) {
    $d['codigo'] = (int)$d['codigo'];
    $d['origem'] = (int)$d['origem'];
    $rows[] = $d;
}

api_json_response($rows);

