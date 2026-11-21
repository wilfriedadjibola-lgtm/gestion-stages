<?php
// Routeur : php -S localhost:8000 -t public public/index.php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Fichiers statiques (interface web)
if ($path === '/') { header('Location: /web/index.html'); exit; }
if (is_file(__DIR__ . $path)) return false;

require __DIR__ . '/../src/Database.php';
require __DIR__ . '/../src/Auth.php';
require __DIR__ . '/../src/DemandeController.php';

try {
    $m = $_SERVER['REQUEST_METHOD'];
    if ($path === '/api/demandes' && $m === 'GET')  DemandeController::lister();
    if ($path === '/api/demandes' && $m === 'POST') DemandeController::creer();
    if (preg_match('#^/api/demandes/(\d+)/valider$#', $path, $x) && $m === 'POST') {
        DemandeController::valider((int)$x[1]);
    }
    DemandeController::json(['erreur' => 'Route inconnue'], 404);
} catch (Throwable $e) {
    error_log($e->getMessage());
    DemandeController::json(['erreur' => 'Erreur serveur'], 500);
}
