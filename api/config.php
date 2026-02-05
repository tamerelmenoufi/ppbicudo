<?php

// Produção (recomendado): defina via variável de ambiente do PHP:
//   PPBICUDO_API_CREDENCIAL=...
//
// Ambiente:
// - Se `APP_ENV=production`, NÃO usamos fallback (a variável deve existir).
// - Caso contrário, usamos a chave de teste `123456` para facilitar desenvolvimento local.

$appEnv = strtolower((string)(getenv('APP_ENV') ?: ''));
$credencial = (string)(getenv('PPBICUDO_API_CREDENCIAL') ?: '');

if ($credencial === '' && $appEnv !== 'production') {
    $credencial = '123456';
}

define('PPBICUDO_API_CREDENCIAL', $credencial);
