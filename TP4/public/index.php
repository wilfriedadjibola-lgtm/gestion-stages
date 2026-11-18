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

use App\Controllers\HealthController;
use App\Services\HealthService;

$config  = require __DIR__ . '/../config/config.php';
$methode = $_SERVER['REQUEST_METHOD'];
$chemin  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($methode === 'GET' && $chemin === '/api/sante') {
    (new HealthController(new HealthService($config['db'])))->sante();
    exit;
}

http_response_code(404);
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['erreur' => 'Route introuvable'], JSON_UNESCAPED_UNICODE);
