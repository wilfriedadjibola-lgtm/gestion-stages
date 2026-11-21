<?php
// Données FICTIVES de démonstration. Usage : php src/seed.php (peut être relancé sans doublons)
declare(strict_types=1);
require __DIR__ . '/Env.php';
require __DIR__ . '/Database.php';
Env::load(__DIR__ . '/../.env');

$db = Database::connect();
$mdp = password_hash('Demo2026!', PASSWORD_DEFAULT);

$utilisateur = function (string $nom, string $prenom, string $email, string $role) use ($db, $mdp): int {
    $q = $db->prepare('SELECT id FROM utilisateur WHERE email = ?');
    $q->execute([$email]);
    if ($id = $q->fetchColumn()) {
        return (int) $id;
    }
    $db->prepare('INSERT INTO utilisateur (nom, prenom, email, mot_de_passe_hash, role, actif) VALUES (?,?,?,?,?,1)')
       ->execute([$nom, $prenom, $email, $mdp, $role]);
    return (int) $db->lastInsertId();
};
$existe = function (string $table, int $uid) use ($db): bool {
    $q = $db->prepare("SELECT 1 FROM $table WHERE utilisateur_id = ?");
    $q->execute([$uid]);
    return (bool) $q->fetchColumn();
};

$db->exec('SET NAMES utf8mb4');
$utilisateur('Responsable', 'Stage', 'admin@demo.test', 'ADMIN');
$enc = $utilisateur('Encadreur', 'Paul', 'encadreur@demo.test', 'ENCADREUR');
$etu = $utilisateur('Etudiant', 'Awa', 'etudiant@demo.test', 'ETUDIANT');

if (!$existe('encadreur', $enc)) {
    $db->prepare('INSERT INTO encadreur (utilisateur_id, grade) VALUES (?,?)')->execute([$enc, 'Enseignant']);
}
if (!$existe('etudiant', $etu)) {
    $db->prepare('INSERT INTO etudiant (utilisateur_id, numero_etudiant, filiere) VALUES (?,?,?)')->execute([$etu, 'DEMO-0001', 'L3 SIL']);
}
if ((int) $db->query('SELECT COUNT(*) FROM entreprise')->fetchColumn() === 0) {
    $db->exec("INSERT INTO entreprise (nom, ville, contact) VALUES ('Acme Tech','Cotonou','rh@acme.test'),('Benin Soft','Abomey-Calavi','contact@beninsoft.test')");
}
echo "Données de démonstration créées (mot de passe : Demo2026!)\n";
