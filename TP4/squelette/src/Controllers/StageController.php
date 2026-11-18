<?php
declare(strict_types=1);

namespace App\Controllers;

final class StageController
{
    /** GET /api/stages : liste vide au départ (le traitement métier viendra plus tard). */
    public function lister(): void
    {
        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([], JSON_UNESCAPED_UNICODE);
    }
}
