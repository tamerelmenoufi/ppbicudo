<?php

// Produção (recomendado): defina via variável de ambiente do PHP:
// PPBICUDO_API_CREDENCIAL= '';
//
// Ambiente:
// - Por padrão, consideramos `APP_ENV=production` (não usamos fallback; a variável deve existir).
// - Para desenvolvimento local, defina `APP_ENV=development` para habilitar fallback `123456`.

$appEnv = strtolower((string)(getenv('APP_ENV') ?: 'production'));
$credencial = (string)(getenv('PPBICUDO_API_CREDENCIAL') ?: '8W4QwTn7tF0s0x1KqkLZp7o3yXQ6h0w0jQmS0ZkqvV9o7jWc2eYy7w7ZrE0m2cQx8JxH8lQbO3gk5rFf8g');

if ($credencial === '' && $appEnv === 'development') {
    $credencial = '123456';
}

define('PPBICUDO_API_CREDENCIAL', $credencial);
