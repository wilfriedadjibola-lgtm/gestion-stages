<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\Database;
use PDOException;

final class StageController
{
    public function __construct(private array $config)
    {
    }

    /** GET /api/stages. La base TP4 ne possède pas de table stage : les entreprises sont les lieux de stage. */
    public function lister(): void
    {
        try {
            $pdo = Database::connecter($this->config);
            $stages = $pdo->query('SELECT id, nom, ville FROM entreprise ORDER BY nom, ville')
                ->fetchAll();
            $this->json(200, $stages);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->json(500, ['erreur' => 'Impossible de charger les stages']);
        }
    }

    private function json(int $code, array $donnees): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($donnees, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }
}
