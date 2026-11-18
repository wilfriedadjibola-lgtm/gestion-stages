<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\HealthService;

final class HealthController
{
    public function __construct(private HealthService $service) {}

    /** GET /api/sante */
    public function sante(): void
    {
        if ($this->service->baseJoignable()) {
            $this->json(200, ['statut' => 'ok', 'base' => 'joignable']);
            return;
        }
        $this->json(503, ['statut' => 'erreur', 'base' => 'injoignable']);
    }

    private function json(int $code, array $donnees): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($donnees, JSON_UNESCAPED_UNICODE);
    }
}
