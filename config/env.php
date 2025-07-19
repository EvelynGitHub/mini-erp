<?php
/**
 * Carrega variáveis do arquivo .env para $_ENV
 */
function loadEnv(string $path = __DIR__ . '/../.env'): void
{
    if (!file_exists($path)) {
        return;
    }

    $vars = parse_ini_file($path, false, INI_SCANNER_TYPED);
    if (!$vars) {
        return;
    }

    foreach ($vars as $key => $value) {
        $_ENV[$key] = $value;
    }
}
