<?php
declare(strict_types=1);

// Autoloader minimal : App\Controllers\X => src/Controllers/X.php
spl_autoload_register(function (string $classe): void {
    $prefixe = 'App\\';
    if (str_starts_with($classe, $prefixe)) {
        $fichier = __DIR__ . '/../src/' . str_replace('\\', '/', substr($classe, strlen($prefixe))) . '.php';
        if (is_file($fichier)) {
            require $fichier;
        }
    }
});

use App\Controllers\AuthController;
use App\Controllers\DemandeController;
use App\Controllers\HealthController;
use App\Controllers\StageController;
use App\Services\AuthService;
use App\Services\HealthService;
$config  = require __DIR__ . '/../config/config.php';
$methode = $_SERVER['REQUEST_METHOD'];
$chemin  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($methode === 'POST' && $chemin === '/api/connexion') {
    (new AuthController(new AuthService($config['db'])))->connexion();
    exit;
}
if ($methode === 'GET' && $chemin === '/api/tableau-de-bord') {
    (new AuthController(new AuthService($config['db'])))->tableauDeBord();
    exit;
}
if ($methode === 'POST' && $chemin === '/api/deconnexion') {
    (new AuthController(new AuthService($config['db'])))->deconnexion();
    exit;
}
if ($methode === 'GET' && $chemin === '/api/sante') {
    (new HealthController(new HealthService($config['db'])))->sante();
    exit;
}

if ($methode === 'GET' && $chemin === '/api/stages') {
    (new StageController($config['db']))->lister();
    exit;
}

if ($methode === 'POST' && $chemin === '/api/demandes') {
    (new DemandeController($config['db']))->creer();
    exit;
}

if (preg_match('#^/api/demandes/(\d+)$#', $chemin, $matches)) {
    $controller = new DemandeController($config['db']);
    $id = (int) $matches[1];
    if ($methode === 'GET') {
        $controller->afficher($id);
        exit;
    }
}

if (preg_match('#^/api/demandes/(\d+)/(valider|refuser)$#', $chemin, $matches) && $methode === 'POST') {
    $controller = new DemandeController($config['db']);
    $id = (int) $matches[1];
    if ($matches[2] === 'valider') {
        $controller->valider($id);
    } else {
        $controller->refuser($id);
    }
    exit;
}

http_response_code(404);
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['erreur' => 'Route introuvable'], JSON_UNESCAPED_UNICODE);
