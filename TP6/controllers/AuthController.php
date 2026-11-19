
<?php

final class AuthController
{
    public function __construct(
        private AuthService $auth
    ) {}

    public function login(array $data): array
    {
        $email = trim((string)($data['email'] ?? ''));
        $password = (string)($data['mot_de_passe'] ?? '');

        if ($email === '' || $password === '') {
            throw new InvalidArgumentException(
                'E-mail et mot de passe obligatoires.',
                400
            );
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(
                'Adresse e-mail invalide.',
                400
            );
        }

        $user = $this->auth->login($email, $password);

        session_regenerate_id(true);
        $_SESSION['user'] = $user;

        return [
            'message' => 'Connexion réussie.',
            'utilisateur' => $user
        ];
    }

    public function logout(): array
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        return [
            'message' => 'Déconnexion réussie.'
        ];
    }
}
