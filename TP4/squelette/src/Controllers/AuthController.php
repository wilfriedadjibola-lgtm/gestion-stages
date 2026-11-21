<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\SessionService;
use PDOException;

final class AuthController
{
    public function __construct(private AuthService $authService)
    {
    }

    public function connexion(): void
    {
        $corps = json_decode(file_get_contents('php://input') ?: '', true);
        if (!is_array($corps)
            || !isset($corps['email'], $corps['mot_de_passe'])
            || !is_string($corps['email'])
            || !is_string($corps['mot_de_passe'])
            || filter_var(trim($corps['email']), FILTER_VALIDATE_EMAIL) === false
            || $corps['mot_de_passe'] === '') {
            $this->json(400, ['erreur' => 'Email et mot de passe valides requis']);
            return;
        }

        try {
            $utilisateur = $this->authService->connecter(trim($corps['email']), $corps['mot_de_passe']);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->json(503, ['erreur' => 'Service d’authentification indisponible']);
            return;
        }

        if ($utilisateur === null) {
            $this->json(401, ['erreur' => 'Identifiants incorrects']);
            return;
        }

        SessionService::demarrer();
        session_regenerate_id(true);
        $_SESSION = ['utilisateur' => $utilisateur, 'jeton_csrf' => bin2hex(random_bytes(32))];
        $this->json(200, [
            'message' => 'Connexion réussie',
            'utilisateur' => $utilisateur,
            'jeton_csrf' => $_SESSION['jeton_csrf'],
        ]);
    }

    public function tableauDeBord(): void
    {
        SessionService::demarrer();
        $utilisateur = $_SESSION['utilisateur'] ?? null;
        if (!is_array($utilisateur)) {
            $this->json(401, ['erreur' => 'Authentification requise']);
            return;
        }
        $this->json(200, [
            'utilisateur' => $utilisateur,
            'jeton_csrf' => SessionService::jetonCsrf(),
        ]);
    }

    public function deconnexion(): void
    {
        SessionService::demarrer();
        if (!SessionService::verifierJetonCsrf()) {
            $this->json(403, ['erreur' => 'Jeton CSRF invalide']);
            return;
        }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $params['path'],
                'domain' => $params['domain'],
                'secure' => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => $params['samesite'] ?? 'Lax',
            ]);
        }
        session_destroy();
        $this->json(200, ['message' => 'Déconnexion réussie']);
    }

    private function json(int $code, array $donnees): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($donnees, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }
}
