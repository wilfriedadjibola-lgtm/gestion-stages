
<?php

final class DemandeService
{
    private const TRANSITIONS = [
        'BROUILLON' => ['SOUMISE'],
        'SOUMISE' => ['VALIDEE', 'REFUSEE'],
        'REFUSEE' => ['BROUILLON'],
        'VALIDEE' => []
    ];

    public function __construct(
        private PDO $pdo,
        private DemandeRepository $demandes
    ) {}

    public function create(
        int $studentId,
        string $entreprise,
        string $dateDebut
    ): int {
        $entreprise = trim($entreprise);

        if ($entreprise === '') {
            throw new InvalidArgumentException(
                'Le nom de l’entreprise est obligatoire.',
                400
            );
        }

        $date = DateTime::createFromFormat('!Y-m-d', $dateDebut);

        if (!$date || $date->format('Y-m-d') !== $dateDebut) {
            throw new InvalidArgumentException(
                'La date doit respecter le format AAAA-MM-JJ.',
                400
            );
        }

        return $this->demandes->create(
            $studentId,
            $entreprise,
            $dateDebut
        );
    }

    public function changeStatus(
        int $demandeId,
        int $actorId,
        string $newStatus,
        ?string $motif
    ): void {
        $newStatus = strtoupper(trim($newStatus));

        $allowed = [
            'SOUMISE',
            'VALIDEE',
            'REFUSEE',
            'BROUILLON'
        ];

        if (!in_array($newStatus, $allowed, true)) {
            throw new InvalidArgumentException(
                'Statut invalide.',
                400
            );
        }

        if ($newStatus === 'REFUSEE' && trim((string)$motif) === '') {
            throw new InvalidArgumentException(
                'Le motif est obligatoire pour un refus.',
                400
            );
        }

        $motif = $motif !== null && trim($motif) !== ''
            ? trim($motif)
            : null;

        $this->pdo->beginTransaction();

        try {
            $demande = $this->demandes->findByIdForUpdate($demandeId);

            if (!$demande) {
                throw new RuntimeException(
                    'Demande introuvable.',
                    404
                );
            }

            $oldStatus = $demande['statut'];
            $possible = self::TRANSITIONS[$oldStatus] ?? [];

            if (!in_array($newStatus, $possible, true)) {
                throw new RuntimeException(
                    "Transition interdite : {$oldStatus} vers {$newStatus}.",
                    409
                );
            }

            $this->demandes->updateStatus(
                $demandeId,
                $newStatus
            );

            $this->demandes->addHistory(
                $demandeId,
                $actorId,
                $oldStatus,
                $newStatus,
                $motif
            );

            $this->pdo->commit();

        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }
}
