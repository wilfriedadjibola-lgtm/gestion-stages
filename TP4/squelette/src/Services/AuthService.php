<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\Database;
use PDO;

final class AuthService
{
    public function __construct(private array $config)
    {
    }

    public function connecter(string $email, string $motDePasse): ?array
    {
        $pdo = Database::connecter($this->config);

        $sql = "SELECT id, email, mot_de_passe_hash, role, nom, prenom
                FROM utilisateur
                WHERE email = :email AND actif = 1
                LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $email]);

        $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$utilisateur ||
            !password_verify($motDePasse, $utilisateur['mot_de_passe_hash'])) {
            return null;
        }

        unset($utilisateur['mot_de_passe_hash']);

        return $utilisateur;
    }
}