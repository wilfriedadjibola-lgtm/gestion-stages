<?php
// Date valide (rejette 31/02)
function dateValide(string $s): bool {
    $d = DateTime::createFromFormat('Y-m-d', $s);
    return $d && $d->format('Y-m-d') === $s;
}

// Refus sans motif
if (trim($_POST['motif'] ?? '') === '') { http_response_code(422); exit('Motif obligatoire'); }

// Validation répétée (idempotence)
$stmt = $pdo->prepare("UPDATE demandes SET statut='validée' WHERE id=? AND statut='en_attente'");
$stmt->execute([$id]);
if ($stmt->rowCount() === 0) { http_response_code(409); exit('Déjà traitée ou inexistante'); }
