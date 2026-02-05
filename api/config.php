<?php

// Produção (recomendado): defina via variável de ambiente do PHP:
//   PPBICUDO_API_CREDENCIAL=...
//
// Ambiente:
// - Por padrão, consideramos `APP_ENV=production` (não usamos fallback; a variável deve existir).
// - Para desenvolvimento local, defina `APP_ENV=development` para habilitar fallback `123456`.

$appEnv = strtolower((string)(getenv('APP_ENV') ?: 'production'));
$credencial = (string)(getenv('PPBICUDO_API_CREDENCIAL') ?: '');

if ($credencial === '' && $appEnv === 'development') {
    $credencial = '123456';
}

define('PPBICUDO_API_CREDENCIAL', $credencial);
