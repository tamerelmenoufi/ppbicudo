<?php

require_once __DIR__ . '/../_bootstrap.php';

api_require_post();
$input = api_read_input();
api_require_auth($input);

$sql = "SELECT codigo, nome
        FROM origens
        WHERE status = '1' AND deletado <> '1'
        ORDER BY nome";

$result = mysqli_query($con, $sql);
if (!$result) {
    api_error('Erro ao consultar origens.', 500);
}

$rows = [];
while ($d = mysqli_fetch_assoc($result)) {
    $rows[] = [
        'codigo' => (int)$d['codigo'],
        'nome' => $d['nome'],
    ];
}

api_json_response($rows);

