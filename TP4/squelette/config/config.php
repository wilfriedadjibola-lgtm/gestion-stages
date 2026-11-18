<?php
declare(strict_types=1);

/** Charge le fichier .env (à la racine du projet) puis retourne la configuration. */
function chargerEnv(string $chemin): void
{
    if (!is_file($chemin)) {
        return;
    }
    foreach (file($chemin, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $ligne) {
        $ligne = trim($ligne);
        if ($ligne === '' || $ligne[0] === '#' || !str_contains($ligne, '=')) {
            continue;
        }
        [$cle, $valeur] = explode('=', $ligne, 2);
        $_ENV[trim($cle)] = trim($valeur);
    }
}

chargerEnv(__DIR__ . '/../.env');

return [
    'db' => [
        'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
        'port' => $_ENV['DB_PORT'] ?? '3306',
        'name' => $_ENV['DB_NAME'] ?? 'projet_sil',
        'user' => $_ENV['DB_USER'] ?? 'root',
        'pass' => $_ENV['DB_PASS'] ?? '',
    ],
];
