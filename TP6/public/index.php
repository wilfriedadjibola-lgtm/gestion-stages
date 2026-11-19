<?php
declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
]);

session_start();

function jsonResponse(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode(
        $data,
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );
    exit;
}

function handleException(Throwable $e): void
{
    $code = (int)$e->getCode();

    if (!in_array($code, [400, 401, 403, 404, 409], true)) {
        error_log($e->getMessage());

        jsonResponse([
            'erreur' => 'Erreur interne du serveur.'
        ], 500);
    }

    jsonResponse([
        'erreur' => $e->getMessage()
    ], $code);
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../repositories/UtilisateurRepository.php';
require_once __DIR__ . '/../repositories/DemandeRepository.php';
require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../services/DemandeService.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/DemandeController.php';

try {
    $pdo = (new Database())->getConnection();

    $userRepo = new UtilisateurRepository($pdo);
    $demandeRepo = new DemandeRepository($pdo);

    $authService = new AuthService($userRepo);
    $demandeService = new DemandeService($pdo, $demandeRepo);

    $authController = new AuthController($authService);
    $demandeController = new DemandeController(
        $demandeRepo,
        $demandeService
    );

    $method = $_SERVER['REQUEST_METHOD'];

    $path = parse_url(
        $_SERVER['REQUEST_URI'],
        PHP_URL_PATH
    );

    $base = '/tp6_api_demandes/public';

    if (str_starts_with($path, $base)) {
        $path = substr($path, strlen($base));
    }

    $path = '/' . trim($path, '/');

    $raw = file_get_contents('php://input');
    $data = [];

    if ($raw !== '') {
        $decoded = json_decode($raw, true);

        if (!is_array($decoded)) {
            throw new InvalidArgumentException(
                'JSON invalide.',
                400
            );
        }

        $data = $decoded;
    }

    if ($method === 'GET' && $path === '/') {
        jsonResponse([
            'message' => 'API de gestion des demandes de stage opérationnelle.'
        ]);
    }

    if ($method === 'POST' && $path === '/api/connexion') {
        jsonResponse($authController->login($data));
    }

    if ($method === 'POST' && $path === '/api/deconnexion') {
        jsonResponse($authController->logout());
    }

    if ($method === 'GET' && $path === '/api/demandes') {
        jsonResponse($demandeController->index());
    }

    if ($method === 'GET' && $path === '/api/mes-demandes') {
        jsonResponse($demandeController->mine());
    }

    if ($method === 'POST' && $path === '/api/demandes') {
        jsonResponse($demandeController->create($data), 201);
    }

    if (
        preg_match(
            '#^/api/demandes/([0-9]+)/historique$#',
            $path,
            $m
        ) && $method === 'GET'
    ) {
        jsonResponse($demandeController->history((int)$m[1]));
    }

    if (
        preg_match(
            '#^/api/demandes/([0-9]+)/statut$#',
            $path,
            $m
        ) && $method === 'PATCH'
    ) {
        jsonResponse(
            $demandeController->changeStatus((int)$m[1], $data)
        );
    }

    if (
        preg_match(
            '#^/api/demandes/([0-9]+)$#',
            $path,
            $m
        ) && $method === 'GET'
    ) {
        jsonResponse($demandeController->show((int)$m[1]));
    }

    jsonResponse([
        'erreur' => 'Route introuvable.'
    ], 404);

} catch (Throwable $e) {
    handleException($e);
}
