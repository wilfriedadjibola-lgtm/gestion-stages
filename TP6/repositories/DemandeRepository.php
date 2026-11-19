
<?php

final class DemandeRepository
{
    public function __construct(private PDO $pdo) {}

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM demande_stage WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public function findByIdForUpdate(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM demande_stage
             WHERE id = :id
             FOR UPDATE'
        );
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public function findByStudent(int $studentId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM demande_stage
             WHERE student_id = :student_id
             ORDER BY created_at DESC'
        );
        $stmt->execute(['student_id' => $studentId]);

        return $stmt->fetchAll();
    }

    public function findAll(): array
    {
        return $this->pdo->query(
            'SELECT d.*, u.nom AS nom_etudiant,
                    u.email AS email_etudiant
             FROM demande_stage d
             INNER JOIN utilisateurs u
                ON u.id = d.student_id
             ORDER BY d.created_at DESC'
        )->fetchAll();
    }

    public function create(
        int $studentId,
        string $entreprise,
        string $dateDebut
    ): int {
        $stmt = $this->pdo->prepare(
            "INSERT INTO demande_stage
             (student_id, entreprise, date_debut, statut)
             VALUES (:student_id, :entreprise, :date_debut, 'BROUILLON')"
        );

        $stmt->execute([
            'student_id' => $studentId,
            'entreprise' => $entreprise,
            'date_debut' => $dateDebut
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function updateStatus(int $id, string $statut): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE demande_stage
             SET statut = :statut
             WHERE id = :id'
        );

        $stmt->execute([
            'statut' => $statut,
            'id' => $id
        ]);
    }

    public function addHistory(
        int $demandeId,
        int $userId,
        string $ancienStatut,
        string $nouveauStatut,
        ?string $motif
    ): void {
        $stmt = $this->pdo->prepare(
            'INSERT INTO historique_demande
             (demande_id, utilisateur_id, ancien_statut,
              nouveau_statut, motif)
             VALUES (:demande_id, :utilisateur_id,
                     :ancien, :nouveau, :motif)'
        );

        $stmt->execute([
            'demande_id' => $demandeId,
            'utilisateur_id' => $userId,
            'ancien' => $ancienStatut,
            'nouveau' => $nouveauStatut,
            'motif' => $motif
        ]);
    }

    public function findHistory(int $demandeId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT h.id, h.demande_id, h.utilisateur_id,
                    h.ancien_statut, h.nouveau_statut,
                    h.motif, h.created_at, u.nom AS nom_utilisateur
             FROM historique_demande h
             INNER JOIN utilisateurs u
                ON u.id = h.utilisateur_id
             WHERE h.demande_id = :id
             ORDER BY h.created_at ASC, h.id ASC'
        );

        $stmt->execute(['id' => $demandeId]);

        return $stmt->fetchAll();
    }
}
