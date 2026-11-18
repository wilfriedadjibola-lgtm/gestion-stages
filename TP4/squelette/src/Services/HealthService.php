<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\Database;
use PDOException;

final class HealthService
{
    public function __construct(private array $configDb) {}

    /** Vrai si la base répond à un SELECT 1. */
    public function baseJoignable(): bool
    {
        try {
            $pdo = Database::connecter($this->configDb);
            return (int) $pdo->query('SELECT 1')->fetchColumn() === 1;
        } catch (PDOException) {
            return false;
        }
    }
}
