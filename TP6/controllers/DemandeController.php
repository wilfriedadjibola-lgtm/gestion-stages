
<?php

final class DemandeController
{
    public function __construct(
        private DemandeRepository $demandes,
        private DemandeService $service
    ) {}

    private function user(): array
    {
        if (empty($_SESSION['user'])) {
            throw new RuntimeException(
                'Authentification requise.',
                401
            );
        }

        return $_SESSION['user'];
    }

    private function responsible(array $user): void
    {
        if ($user['role'] !== 'RESPONSABLE') {
            throw new RuntimeException(
                'Accès réservé au responsable.',
                403
            );
        }
    }

    public function index(): array
    {
        $user = $this->user();
        $this->responsible($user);

        return ['demandes' => $this->demandes->findAll()];
    }

    public function mine(): array
    {
        $user = $this->user();

        if ($user['role'] !== 'ETUDIANT') {
            throw new RuntimeException(
                'Accès réservé à l’étudiant.',
                403
            );
        }

        return [
            'demandes' => $this->demandes->findByStudent(
                (int)$user['id']
            )
        ];
    }

    public function show(int $id): array
    {
        $user = $this->user();
        $demande = $this->demandes->findById($id);

        if (!$demande) {
            throw new RuntimeException(
                'Demande introuvable.',
                404
            );
        }

        if (
            $user['role'] !== 'RESPONSABLE' &&
            (int)$demande['student_id'] !== (int)$user['id']
        ) {
            throw new RuntimeException(
                'Accès interdit à cette demande.',
                403
            );
        }

        return ['demande' => $demande];
    }

    public function create(array $data): array
    {
        $user = $this->user();

        if ($user['role'] !== 'ETUDIANT') {
            throw new RuntimeException(
                'Seul un étudiant peut créer une demande.',
                403
            );
        }

        $id = $this->service->create(
            (int)$user['id'],
            (string)($data['entreprise'] ?? ''),
            (string)($data['date_debut'] ?? '')
        );

        return [
            'message' => 'Demande créée.',
            'id' => $id
        ];
    }

    public function changeStatus(int $id, array $data): array
    {
        $user = $this->user();

        $demande = $this->demandes->findById($id);

        if (!$demande) {
            throw new RuntimeException(
                'Demande introuvable.',
                404
            );
        }

        $status = strtoupper(trim((string)($data['statut'] ?? '')));
        $motif = isset($data['motif'])
            ? (string)$data['motif']
            : null;

        if ($status === 'SOUMISE' || $status === 'BROUILLON') {
            if (
                $user['role'] !== 'ETUDIANT' ||
                (int)$demande['student_id'] !== (int)$user['id']
            ) {
                throw new RuntimeException(
                    'Action interdite pour cet utilisateur.',
                    403
                );
            }
        } elseif ($status === 'VALIDEE' || $status === 'REFUSEE') {
            $this->responsible($user);
        } else {
            throw new InvalidArgumentException(
                'Statut invalide.',
                400
            );
        }

        $this->service->changeStatus(
            $id,
            (int)$user['id'],
            $status,
            $motif
        );

        return ['message' => 'Statut mis à jour.'];
    }

    public function history(int $id): array
    {
        $user = $this->user();
        $demande = $this->demandes->findById($id);

        if (!$demande) {
            throw new RuntimeException(
                'Demande introuvable.',
                404
            );
        }

        if (
            $user['role'] !== 'RESPONSABLE' &&
            (int)$demande['student_id'] !== (int)$user['id']
        ) {
            throw new RuntimeException(
                'Accès interdit à cet historique.',
                403
            );
        }

        return [
            'historique' => $this->demandes->findHistory($id)
        ];
    }
}
