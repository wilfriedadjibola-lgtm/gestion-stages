<?php
// Charge .env (format CLE=valeur) et expose la configuration
$env = [];
$fichier = __DIR__ . '/../.env';
if (is_file($fichier)) {
    foreach (file($fichier, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $ligne) {
        if ($ligne[0] === '#' || strpos($ligne, '=') === false) continue;
        [$k, $v] = explode('=', $ligne, 2);
        $env[trim($k)] = trim($v);
    }
}

return [
    'db_host' => $env['DB_HOST'] ?? '127.0.0.1',
    'db_port' => $env['DB_PORT'] ?? '3306',
    'db_name' => $env['DB_NAME'] ?? 'tp7',
    'db_user' => $env['DB_USER'] ?? 'root',
    'db_pass' => $env['DB_PASS'] ?? '',
];
