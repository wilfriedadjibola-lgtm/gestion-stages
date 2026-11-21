<?php
class DemandeController
{
    public static function lister(): void
    {
        $user = self::exigerUtilisateur();
        $rows = Database::get()
            ->query('SELECT id, titre, description, statut FROM demandes ORDER BY id')
            ->fetchAll();
        self::json($rows);
    }

    public static function creer(): void
    {
        $user = self::exigerUtilisateur();
        $data = json_decode(file_get_contents('php://input'), true);
        $titre = is_array($data) ? trim((string)($data['titre'] ?? '')) : '';
        $desc  = is_array($data) ? trim((string)($data['description'] ?? '')) : '';

        if ($titre === '' || $desc === '' || mb_strlen($titre) > 100) {
            self::json(['erreur' => 'Données invalides'], 400);
        }
        $st = Database::get()->prepare(
            'INSERT INTO demandes (titre, description, utilisateur_id) VALUES (?, ?, ?)'
        );
        $st->execute([$titre, $desc, $user['id']]);
        self::json(['id' => (int)Database::get()->lastInsertId(), 'titre' => $titre,
                    'description' => $desc, 'statut' => 'en attente'], 201);
    }

    public static function valider(int $id): void
    {
        $user = self::exigerUtilisateur();
        if ($user['role'] !== 'valideur') {
            self::json(['erreur' => 'Réservé au valideur'], 403);
        }
        $st = Database::get()->prepare("UPDATE demandes SET statut = 'validée' WHERE id = ?");
        $st->execute([$id]);
        $st = Database::get()->prepare('SELECT id, titre, description, statut FROM demandes WHERE id = ?');
        $st->execute([$id]);
        $d = $st->fetch();
        if (!$d) self::json(['erreur' => 'Demande introuvable'], 404);
        self::json($d);
    }

    private static function exigerUtilisateur(): array
    {
        $u = Auth::utilisateur();
        if (!$u) self::json(['erreur' => 'Non authentifié'], 401);
        return $u;
    }

    public static function json($data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
