<?php
class Auth
{
    /** Retourne l'utilisateur lié au jeton Bearer, ou null (-> 401). */
    public static function utilisateur(): ?array
    {
        $h = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (!preg_match('/^Bearer\s+(.+)$/i', $h, $m)) return null;
        $st = Database::get()->prepare('SELECT * FROM utilisateurs WHERE token = ?');
        $st->execute([trim($m[1])]);
        return $st->fetch() ?: null;
    }
}
