<?php
declare(strict_types=1);

final class Api
{
    private const GESTION = ['ADMIN', 'RESPONSABLE'];
    private ?PDO $db = null;

    private function db(): PDO
    {
        return $this->db ??= Database::connect();
    }

    public static function json(int $code, mixed $data): never
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function corps(): array
    {
        $d = json_decode((string) file_get_contents('php://input'), true);
        return is_array($d) ? $d : [];
    }

    public function handle(string $m, string $p): void
    {
        $p = rtrim($p, '/');
        if ($m === 'GET' && $p === '/api/sante') $this->sante();
        if ($m === 'POST' && $p === '/api/login') $this->login();

        $u = $_SESSION['user'] ?? null;
        if (!$u) self::json(401, ['erreur' => 'Authentification requise']);
        if ($m !== 'GET' && !hash_equals($_SESSION['csrf'] ?? '', $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '')) {
            self::json(403, ['erreur' => 'Jeton CSRF invalide']);
        }

        if ($m === 'GET' && $p === '/api/moi') self::json(200, ['user' => $u, 'csrf' => $_SESSION['csrf']]);
        if ($m === 'POST' && $p === '/api/logout') { session_destroy(); self::json(200, ['ok' => true]); }
        if ($m === 'GET' && $p === '/api/entreprises') self::json(200, $this->db()->query('SELECT id, nom, ville FROM entreprise ORDER BY nom')->fetchAll());
        if ($m === 'GET' && $p === '/api/encadreurs') $this->encadreurs($u);
        if ($m === 'GET' && $p === '/api/stats') $this->stats($u);
        if ($m === 'GET' && $p === '/api/demandes') $this->lister($u);
        if ($m === 'POST' && $p === '/api/demandes') $this->creer($u);
        if ($m === 'POST' && preg_match('#^/api/demandes/(\d+)/transition$#', $p, $r)) $this->transition($u, (int) $r[1]);
        if ($m === 'POST' && preg_match('#^/api/demandes/(\d+)/encadreur$#', $p, $r)) $this->affecter($u, (int) $r[1]);

        self::json(404, ['erreur' => 'Route inconnue']);
    }

    private function sante(): never
    {
        try {
            $this->db()->query('SELECT 1');
            self::json(200, ['statut' => 'ok', 'base' => 'ok']);
        } catch (Throwable) {
            self::json(503, ['statut' => 'erreur', 'base' => 'injoignable']);
        }
    }

    private function login(): never
    {
        $b = $this->corps();
        $st = $this->db()->prepare('SELECT id, nom, prenom, mot_de_passe_hash, role FROM utilisateur WHERE email = ? AND actif = 1');
        $st->execute([trim((string) ($b['email'] ?? ''))]);
        $u = $st->fetch();
        if (!$u || !password_verify((string) ($b['mot_de_passe'] ?? ''), $u['mot_de_passe_hash'])) {
            self::json(401, ['erreur' => 'Identifiants incorrects']);
        }
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => (int) $u['id'], 'nom' => $u['prenom'] . ' ' . $u['nom'], 'role' => $u['role']];
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
        self::json(200, ['user' => $_SESSION['user'], 'csrf' => $_SESSION['csrf']]);
    }

    private function exigerGestion(array $u): void
    {
        if (!in_array($u['role'], self::GESTION, true)) self::json(403, ['erreur' => 'Accès refusé pour ce rôle']);
    }

    private function historiser(int $demande, ?string $ancien, string $nouveau, int $auteur, ?string $motif): void
    {
        $this->db()->prepare('INSERT INTO historique (demande_id, ancien_statut, nouveau_statut, auteur_id, motif, date_evenement) VALUES (?,?,?,?,?,NOW())')
            ->execute([$demande, $ancien, $nouveau, $auteur, $motif]);
    }

    private function encadreurs(array $u): never
    {
        $this->exigerGestion($u);
        self::json(200, $this->db()->query("SELECT en.id, CONCAT(u.prenom,' ',u.nom) AS nom FROM encadreur en JOIN utilisateur u ON u.id = en.utilisateur_id ORDER BY u.nom")->fetchAll());
    }

    private function stats(array $u): never
    {
        $this->exigerGestion($u);
        $par = $this->db()->query('SELECT statut, COUNT(*) AS nombre FROM demande_stage GROUP BY statut')->fetchAll();
        $sans = (int) $this->db()->query('SELECT COUNT(*) FROM v_dossiers_sans_encadreur')->fetchColumn();
        self::json(200, ['par_statut' => $par, 'sans_encadreur' => $sans]);
    }

    private function lister(array $u): never
    {
        $filtre = match ($u['role']) {
            'ETUDIANT'  => 'e.utilisateur_id = :uid',
            'ENCADREUR' => 'd.encadreur_id = (SELECT id FROM encadreur WHERE utilisateur_id = :uid)',
            default     => '1 = 1',
        };
        $st = $this->db()->prepare("SELECT d.id, d.statut, d.motif_refus, d.date_depot, d.date_decision, d.encadreur_id,
                en.nom AS entreprise, CONCAT(us.prenom,' ',us.nom) AS etudiant
            FROM demande_stage d
            JOIN etudiant e ON e.id = d.etudiant_id
            JOIN utilisateur us ON us.id = e.utilisateur_id
            JOIN entreprise en ON en.id = d.entreprise_id
            WHERE $filtre ORDER BY d.id DESC");
        $st->execute(in_array($u['role'], ['ETUDIANT', 'ENCADREUR'], true) ? ['uid' => $u['id']] : []);
        self::json(200, $st->fetchAll());
    }

    private function creer(array $u): never
    {
        if ($u['role'] !== 'ETUDIANT') self::json(403, ['erreur' => 'Réservé aux étudiants']);
        $ent = (int) ($this->corps()['entreprise_id'] ?? 0);

        $q = $this->db()->prepare('SELECT 1 FROM entreprise WHERE id = ?');
        $q->execute([$ent]);
        if (!$q->fetchColumn()) self::json(400, ['erreur' => 'Entreprise introuvable']);

        $q = $this->db()->prepare('SELECT id FROM etudiant WHERE utilisateur_id = ?');
        $q->execute([$u['id']]);
        $etu = $q->fetchColumn();
        if (!$etu) self::json(400, ['erreur' => 'Profil étudiant introuvable']);

        $pdo = $this->db();
        $pdo->beginTransaction();
        $pdo->prepare("INSERT INTO demande_stage (etudiant_id, entreprise_id, statut, date_depot) VALUES (?,?,'BROUILLON',NOW())")
            ->execute([$etu, $ent]);
        $id = (int) $pdo->lastInsertId();
        $this->historiser($id, 'BROUILLON', 'BROUILLON', $u['id'], 'Création');
        $pdo->commit();
        self::json(201, ['id' => $id, 'statut' => 'BROUILLON']);
    }

    private function transition(array $u, int $id): never
    {
        $b = $this->corps();
        $nouveau = (string) ($b['statut'] ?? '');
        $motif = isset($b['motif']) ? trim((string) $b['motif']) : null;

        $pdo = $this->db();
        $pdo->beginTransaction();
        $st = $pdo->prepare('SELECT d.statut, e.utilisateur_id FROM demande_stage d JOIN etudiant e ON e.id = d.etudiant_id WHERE d.id = ? FOR UPDATE');
        $st->execute([$id]);
        $d = $st->fetch();
        if (!$d) { $pdo->rollBack(); self::json(404, ['erreur' => 'Demande introuvable']); }

        $roles = Statut::rolesPour($nouveau);
        $gestion = $roles !== ['ETUDIANT'];
        $autorise = in_array($u['role'], $roles, true) && ($gestion || (int) $d['utilisateur_id'] === $u['id']);
        if (!$autorise) { $pdo->rollBack(); self::json(403, ['erreur' => 'Opération non autorisée']); }

        $err = Statut::verifier($d['statut'], $nouveau, $motif);
        if ($err) { $pdo->rollBack(); self::json(422, ['erreur' => $err]); }

        $pdo->prepare('UPDATE demande_stage SET statut = ?, motif_refus = ?, date_decision = ?, date_depot = IF(? = \'SOUMISE\', NOW(), date_depot) WHERE id = ?')->execute([
            $nouveau,
            $nouveau === 'REFUSEE' ? $motif : null,
            in_array($nouveau, ['VALIDEE', 'REFUSEE'], true) ? date('Y-m-d H:i:s') : null,
            $nouveau,
            $id,
        ]);
        $this->historiser($id, $d['statut'], $nouveau, $u['id'], $motif);
        $pdo->commit();
        self::json(200, ['id' => $id, 'statut' => $nouveau]);
    }

    private function affecter(array $u, int $id): never
    {
        $this->exigerGestion($u);
        $enc = (int) ($this->corps()['encadreur_id'] ?? 0);
        $pdo = $this->db();

        $q = $pdo->prepare('SELECT 1 FROM encadreur WHERE id = ?');
        $q->execute([$enc]);
        if (!$q->fetchColumn()) self::json(400, ['erreur' => 'Encadreur introuvable']);

        $q = $pdo->prepare('SELECT statut FROM demande_stage WHERE id = ?');
        $q->execute([$id]);
        $statut = $q->fetchColumn();
        if ($statut === false) self::json(404, ['erreur' => 'Demande introuvable']);
        if ($statut !== 'VALIDEE') self::json(422, ['erreur' => 'Seule une demande validée peut recevoir un encadreur']);

        $pdo->prepare('UPDATE demande_stage SET encadreur_id = ? WHERE id = ?')->execute([$enc, $id]);
        $this->historiser($id, 'VALIDEE', 'VALIDEE', $u['id'], "Affectation encadreur #$enc");
        self::json(200, ['id' => $id, 'encadreur_id' => $enc]);
    }
}
