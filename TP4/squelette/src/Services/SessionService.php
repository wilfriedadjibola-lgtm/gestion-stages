<?php
declare(strict_types=1);

namespace App\Services;

final class SessionService
{
    public static function demarrer(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        session_set_cookie_params([
            'httponly' => true,
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'samesite' => 'Lax',
            'path' => '/',
        ]);
        session_start();
    }

    public static function jetonCsrf(): string
    {
        self::demarrer();
        if (!isset($_SESSION['jeton_csrf']) || !is_string($_SESSION['jeton_csrf'])) {
            $_SESSION['jeton_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['jeton_csrf'];
    }

    public static function verifierJetonCsrf(): bool
    {
        self::demarrer();
        $fourni = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        $attendu = $_SESSION['jeton_csrf'] ?? '';
        return is_string($fourni) && is_string($attendu) && $fourni !== ''
            && hash_equals($attendu, $fourni);
    }
}
