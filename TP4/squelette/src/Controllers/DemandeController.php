<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\Database;
use App\Services\SessionService;
use DateTimeImmutable;
use PDO;
use PDOException;

final class DemandeController
{
    public function __construct(private array $config)
    {
    }

    /** POST /api/demandes; stage_id is retained as the TP8 alias for entreprise_id. */
    public function creer(): void
    {
        $body = $this->corpsJson();
        if ($body === null
            || !isset($body['etudiant_id'])
            || !$this->entierPositif($body['etudiant_id'])
            || !isset($body['stage_id']) && !isset($body['entreprise_id'])) {
            $this->json(400, ['erreur' => 'etudiant_id et stage_id (entiers positifs) requis']);
            return;
        }
        $enterpriseId = $body['stage_id'] ?? $body['entreprise_id'];
        if (!$this->entierPositif($enterpriseId)) {
            $this->json(400, ['erreur' => 'stage_id doit être un entier positif']);
            return;
        }

        $user = $this->utilisateurConnecte();
        if ($user === null) {
            return;
        }
        if (($user['role'] ?? null) !== 'ETUDIANT') {
            $this->json(403, ['erreur' => 'Accès réservé aux étudiants']);
            return;
        }
        if (!$this->csrfValide()) {
            return;
        }

        try {
            $pdo = Database::connecter($this->config);
            $pdo->beginTransaction();
            $stmt = $pdo->prepare('SELECT id FROM etudiant WHERE utilisateur_id = :uid FOR UPDATE');
            $stmt->execute(['uid' => $user['id']]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$student) {
                $pdo->rollBack();
                $this->json(403, ['erreur' => 'Profil étudiant introuvable']);
                return;
            }
            if ((int) $student['id'] !== (int) $body['etudiant_id']) {
                $pdo->rollBack();
                $this->json(403, ['erreur' => 'Le compte ne correspond pas à cet étudiant']);
                return;
            }
            $stmt = $pdo->prepare('SELECT id FROM entreprise WHERE id = :id');
            $stmt->execute(['id' => $enterpriseId]);
            if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
                $pdo->rollBack();
                $this->json(404, ['erreur' => 'Stage/entreprise introuvable']);
                return;
            }
            $stmt = $pdo->prepare(
                "INSERT INTO demande_stage (etudiant_id, entreprise_id, statut, date_depot)
                 VALUES (:etudiant_id, :entreprise_id, 'BROUILLON', NOW())"
            );
            $stmt->execute(['etudiant_id' => $student['id'], 'entreprise_id' => $enterpriseId]);
            $id = (int) $pdo->lastInsertId();
            $pdo->commit();
            $this->json(201, ['message' => 'Demande créée avec succès', 'demande' => [
                'id' => $id,
                'etudiant_id' => (int) $student['id'],
                'stage_id' => (int) $enterpriseId,
                'entreprise_id' => (int) $enterpriseId,
                'statut' => 'BROUILLON',
            ]]);
        } catch (PDOException $e) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log($e->getMessage());
            $this->json(500, ['erreur' => 'Erreur lors de la création de la demande']);
        }
    }

    /** GET /api/demandes/{id}; students may only see their own requests. */
    public function afficher(int $id): void
    {
        $user = $this->utilisateurConnecte();
        if ($user === null) {
            return;
        }
        if ($id < 1) {
            $this->json(404, ['erreur' => 'Demande introuvable']);
            return;
        }
        try {
            $pdo = Database::connecter($this->config);
            $stmt = $pdo->prepare(
                'SELECT d.id, d.etudiant_id, d.entreprise_id, d.statut, d.motif_refus,
                        d.date_depot, d.date_decision, e.utilisateur_id
                 FROM demande_stage d JOIN etudiant e ON e.id = d.etudiant_id WHERE d.id = :id'
            );
            $stmt->execute(['id' => $id]);
            $demand = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$demand) {
                $this->json(404, ['erreur' => 'Demande introuvable']);
                return;
            }
            if (($user['role'] ?? '') === 'ETUDIANT' && (int) $demand['utilisateur_id'] !== (int) $user['id']) {
                $this->json(403, ['erreur' => 'Accès interdit à cette demande']);
                return;
            }
            unset($demand['utilisateur_id']);
            $this->json(200, ['demande' => $demand]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->json(500, ['erreur' => 'Erreur lors de la lecture de la demande']);
        }
    }

    public function valider(int $id): void
    {
        $body = $this->debutDecision();
        if ($body === null) {
            return;
        }
        $date = $this->dateDecision($body['date_decision'] ?? null);
        if ($date === null) {
            $this->json(400, ['erreur' => 'date_decision doit être une date valide au format AAAA-MM-JJ']);
            return;
        }
        $this->changerStatut($id, 'VALIDEE', null, $date);
    }

    public function refuser(int $id): void
    {
        $body = $this->debutDecision();
        if ($body === null) {
            return;
        }
        $motif = $body['motif'] ?? null;
        if (!is_string($motif) || trim($motif) === '' || strlen(trim($motif)) > 255) {
            $this->json(400, ['erreur' => 'Un motif de refus non vide (255 caractères maximum) est requis']);
            return;
        }
        $date = $this->dateDecision($body['date_decision'] ?? null);
        if ($date === null) {
            $this->json(400, ['erreur' => 'date_decision doit être une date valide au format AAAA-MM-JJ']);
            return;
        }
        $this->changerStatut($id, 'REFUSEE', trim($motif), $date);
    }

    private function debutDecision(): ?array
    {
        $user = $this->utilisateurConnecte();
        if ($user === null) {
            return null;
        }
        if (!in_array($user['role'] ?? '', ['ENCADREUR', 'SECRETARIAT', 'RESPONSABLE', 'ADMIN'], true)) {
            $this->json(403, ['erreur' => 'Autorisation insuffisante']);
            return null;
        }
        if (!$this->csrfValide()) {
            return null;
        }
        $body = $this->corpsJson();
        if ($body === null) {
            $this->json(400, ['erreur' => 'Corps JSON invalide']);
            return null;
        }
        return $body;
    }

    private function changerStatut(int $id, string $nouveauStatut, ?string $motif, string $date): void
    {
        if ($id < 1) {
            $this->json(404, ['erreur' => 'Demande introuvable']);
            return;
        }
        try {
            $pdo = Database::connecter($this->config);
            $pdo->beginTransaction();
            $stmt = $pdo->prepare('SELECT statut FROM demande_stage WHERE id = :id FOR UPDATE');
            $stmt->execute(['id' => $id]);
            $demand = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$demand) {
                $pdo->rollBack();
                $this->json(404, ['erreur' => 'Demande introuvable']);
                return;
            }
            if (!in_array($demand['statut'], ['BROUILLON', 'SOUMISE'], true)) {
                $pdo->rollBack();
                $this->json(409, ['erreur' => 'Cette demande a déjà été traitée']);
                return;
            }

            $stmt = $pdo->prepare(
                'UPDATE demande_stage SET statut = :statut, motif_refus = :motif, date_decision = :date WHERE id = :id'
            );
            $stmt->execute(['statut' => $nouveauStatut, 'motif' => $motif, 'date' => $date, 'id' => $id]);
            $stmt = $pdo->prepare(
                'INSERT INTO historique (demande_id, ancien_statut, nouveau_statut, auteur_id, motif)
                 VALUES (:demande, :ancien, :nouveau, :auteur, :motif)'
            );
            $stmt->execute([
                'demande' => $id,
                'ancien' => $demand['statut'],
                'nouveau' => $nouveauStatut,
                'auteur' => $_SESSION['utilisateur']['id'],
                'motif' => $motif,
            ]);
            $pdo->commit();
            $this->json(200, ['message' => 'Demande mise à jour', 'demande_id' => $id, 'statut' => $nouveauStatut]);
        } catch (PDOException $e) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log($e->getMessage());
            $this->json(500, ['erreur' => 'Erreur lors de la mise à jour de la demande']);
        }
    }

    private function utilisateurConnecte(): ?array
    {
        SessionService::demarrer();
        $user = $_SESSION['utilisateur'] ?? null;
        if (!is_array($user) || !isset($user['id'], $user['role'])) {
            $this->json(401, ['erreur' => 'Authentification requise']);
            return null;
        }
        return $user;
    }

    private function csrfValide(): bool
    {
        if (!SessionService::verifierJetonCsrf()) {
            $this->json(403, ['erreur' => 'Jeton CSRF invalide']);
            return false;
        }
        return true;
    }

    private function corpsJson(): ?array
    {
        $body = json_decode(file_get_contents('php://input') ?: '', true);
        return is_array($body) ? $body : null;
    }

    private function entierPositif(mixed $value): bool
    {
        return is_int($value) && $value > 0;
    }

    private function dateDecision(mixed $value): ?string
    {
        if ($value === null) {
            return date('Y-m-d 00:00:00');
        }

        if (!is_string($value)) {
            return null;
        }

        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        $errors = DateTimeImmutable::getLastErrors();

        if (
            $date === false
            || ($errors !== false && (
                $errors['warning_count'] > 0
                || $errors['error_count'] > 0
            ))
            || $date->format('Y-m-d') !== $value
        ) {
            return null;
        }

        if ($date->format('Y-m-d') < date('Y-m-d')) {
            return null;
        }

        return $date->format('Y-m-d 00:00:00');
    }
    private function json(int $code, array $donnees): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($donnees, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }
}
