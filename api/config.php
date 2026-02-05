<?php

// Prefer setting the credential via environment variable:
putenv('PPBICUDO_API_CREDENCIAL=123456');
// If not set, you can hardcode below (not recommended for shared repos).

define('PPBICUDO_API_CREDENCIAL', getenv('PPBICUDO_API_CREDENCIAL') ?: '');

