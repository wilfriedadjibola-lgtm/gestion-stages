# Correction type - TP8

## Cas limites (attendu)
- Demande inexistante : vérifier `if (!$row)` puis `http_response_code(404)` + message neutre.
- Refus sans motif : contrôle côté serveur (`trim($motif) === ''`), aucun changement de statut.
- Validation répétée : `UPDATE demandes SET statut='validée' WHERE id=? AND statut='en_attente'` ; si `rowCount() === 0` -> refus.
- Date invalide : `DateTime::createFromFormat('Y-m-d', $s)` + comparaison avec `format()` (rejette le 31/02).

## Autorisation
Étudiant B lit le dossier de A -> 403. Contrôle côté serveur à chaque requête (voir code/autorisation.php).
Preuve : onglet Réseau des outils développeur (code 403).

## Chaîne « 1 OR 1=1 »
Avec requête préparée + `filter_var(..., FILTER_VALIDATE_INT)`, la chaîne est rejetée (400) ou traitée comme valeur.
Si la requête est concaténée, le test échoue : faille à corriger avec `prepare()`.

## Hachage des mots de passe
`password_hash($mdp, PASSWORD_DEFAULT)` à l'inscription, `password_verify()` à la connexion.
Preuve : code/verif_hash.sql -> préfixe `$2y$10$`, longueur 60. Deux mêmes mots de passe donnent deux hash différents (sel).

## Protection CSRF retenue : jeton synchronisé
1. Jeton généré en session (`random_bytes`).
2. Champ caché dans chaque formulaire POST.
3. Vérification avec `hash_equals`, sinon 403.
4. Compléments : actions sensibles en POST uniquement, cookie SameSite/HttpOnly, `session_regenerate_id(true)` après connexion.
(voir code/csrf.php)
