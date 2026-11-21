<?php
// Exécuter une seule fois depuis le navigateur, puis supprimer ce fichier.
// Comptes fictifs de TP : étudiant et responsable.
require_once __DIR__ . '/../config/database.php';

$pdo = (new Database())->getConnection();

$users = [
    ['Étudiant Test', 'etudiant@test.local', 'Etudiant123!', 'ETUDIANT'],
    ['Responsable Test', 'responsable@test.local', 'Responsable123!', 'RESPONSABLE'],
];

$sql = 'INSERT INTO utilisateurs (nom, email, mot_de_passe, role)
        VALUES (:nom, :email, :password, :role)
        ON DUPLICATE KEY UPDATE
        nom = VALUES(nom), mot_de_passe = VALUES(mot_de_passe), role = VALUES(role)';
$stmt = $pdo->prepare($sql);

foreach ($users as [$nom, $email, $password, $role]) {
    $stmt->execute([
        'nom' => $nom,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'role' => $role,
    ]);
}

echo 'Comptes de test créés.<br>';
echo 'Étudiant : etudiant@test.local / Etudiant123!<br>';
echo 'Responsable : responsable@test.local / Responsable123!<br>';
echo '<strong>Supprime maintenant ce fichier du dossier tests.</strong>';
