<?php
declare(strict_types=1);

namespace App\Controllers;

final class DemandeController
{
    /** POST /api/demandes : contrat posé, création à implémenter plus tard. */
    public function creer(): void
    {
        $corps = json_decode(file_get_contents('php://input') ?: '', true);

        if (!is_array($corps)
            || !isset($corps['etudiant_id'], $corps['stage_id'])
            || !is_int($corps['etudiant_id'])
            || !is_int($corps['stage_id'])) {
            $this->json(400, ['erreur' => 'Données invalides : etudiant_id et stage_id (entiers) requis']);
            return;
        }

        $this->json(501, ['erreur' => 'Création non implémentée dans ce TP']);
    }

    private function json(int $code, array $donnees): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($donnees, JSON_UNESCAPED_UNICODE);
    }
}
