<?php
// À la connexion
session_regenerate_id(true);
$_SESSION['csrf'] = bin2hex(random_bytes(32));

// Dans chaque formulaire :
// <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">

// À la réception d'un POST
if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
    http_response_code(403); exit('Jeton CSRF invalide');
}
