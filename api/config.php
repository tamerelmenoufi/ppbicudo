<?php

// Produção (recomendado): defina via variável de ambiente do PHP:
//   PPBICUDO_API_CREDENCIAL=...
//
// Desenvolvimento: se não houver variável configurada, a API usa a chave de teste abaixo.
// Troque/remoça antes de publicar em produção.

define('PPBICUDO_API_CREDENCIAL', getenv('PPBICUDO_API_CREDENCIAL') ?: '123456');
