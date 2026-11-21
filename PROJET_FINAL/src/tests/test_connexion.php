<?php
// Vérifie la connexion PDO à MySQL. Usage : php src/tests/test_connexion.php
declare(strict_types=1);
require __DIR__ . '/../Env.php';
require __DIR__ . '/../Database.php';
Env::load(__DIR__ . '/../../.env');

try {
    Database::connect()->query('SELECT 1');
    echo "Connexion MySQL : OK\n";
} catch (Throwable $e) {
    echo "Connexion MySQL : ECHEC (" . $e->getMessage() . ")\n";
    exit(1);
}
