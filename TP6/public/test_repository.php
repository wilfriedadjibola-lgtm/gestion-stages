<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../repositories/DemandeRepository.php';

try {
    $pdo = (new Database())->getConnection();
    $repo = new DemandeRepository($pdo);

    echo '<h2>Test DemandeRepository</h2>';
    echo '<h3>Demande numéro 1</h3><pre>';
    var_dump($repo->findById(1));
    echo '</pre><h3>Demandes de l’étudiant numéro 1</h3><pre>';
    var_dump($repo->findByStudent(1));
    echo '</pre>';
} catch (Throwable $e) {
    http_response_code(500);
    echo htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
