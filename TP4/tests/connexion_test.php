<?php
declare(strict_types=1);

require __DIR__ . '/../src/Repositories/Database.php';
$config = require __DIR__ . '/../config/config.php';

use App\Repositories\Database;

try {
    $pdo = Database::connecter($config['db']);
    $ok  = (int) $pdo->query('SELECT 1')->fetchColumn() === 1;
    echo $ok ? "[OK] Connexion PDO réussie\n" : "[ECHEC] Réponse inattendue\n";
    exit($ok ? 0 : 1);
} catch (PDOException $e) {
    echo "[ECHEC] Connexion impossible : " . $e->getMessage() . "\n";
    exit(1);
}
