<?php
session_start();
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if ($id === false || $id === null) { http_response_code(400); exit('Identifiant invalide'); }

$stmt = $pdo->prepare('SELECT * FROM dossiers WHERE id = ?');
$stmt->execute([$id]);
$dossier = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$dossier) { http_response_code(404); exit('Introuvable'); }
if ($dossier['etudiant_id'] !== $_SESSION['user_id'] && ($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403); exit('Accès interdit');
}
