<?php
// Point d'entrée : php -S localhost:8000 -t Public Public/index.php
declare(strict_types=1);

$chemin = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (!str_starts_with($chemin, '/api/')) {
    $fichier = __DIR__ . $chemin;
    if ($chemin !== '/' && is_file($fichier) && !str_ends_with($chemin, '.php')) {
        return false; // fichier statique (css, js)
    }
    readfile(__DIR__ . '/index.html');
    return;
}

require __DIR__ . '/../src/bootstrap.php';
