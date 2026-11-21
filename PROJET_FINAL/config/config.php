<?php
declare(strict_types=1);

return [
    'db' => [
        'host' => Env::get('DB_HOST', '127.0.0.1'),
        'port' => Env::get('DB_PORT', '3306'),
        'name' => Env::get('DB_NAME', 'projet_sil_tp9'),
        'user' => Env::get('DB_USER', 'tp9_app'),
        'pass' => Env::get('DB_PASS', ''),
    ],
];
