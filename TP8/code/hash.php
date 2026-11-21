<?php
// Inscription
$hash = password_hash($_POST['mdp'], PASSWORD_DEFAULT);
// Connexion
if (password_verify($_POST['mdp'], $hash)) { /* OK */ }
