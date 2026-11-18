<?php
declare(strict_types=1);

namespace App\Repositories;

use PDO;
use PDOException;

final class Database
{
    /**
     * Ouvre une connexion PDO configurée pour lever des exceptions.
     *
     * @throws PDOException si la base est injoignable
     */
    public static function connecter(array $config): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $config['host'],
            $config['port'],
            $config['name']
        );

        return new PDO($dsn, $config['user'], $config['pass'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
}
