# Tableau des cas de test

Statut : **Auto** = exécuté par `php src/tests/test_statuts.php` ; **Manuel** = à exécuter dans l'application puis à prouver par une capture dans `Preuves/`.

| N° | Règle / exigence | Scénario | Résultat attendu | Type | Preuve |
|----|------------------|----------|------------------|------|--------|
| TC-01 | Cycle de statut | BROUILLON → SOUMISE | Autorisé | Auto | 05_tests_statuts |
| TC-02 | Cycle de statut | SOUMISE → VALIDEE | Autorisé | Auto | 05_tests_statuts |
| TC-03 | Cycle de statut | SOUMISE → REFUSEE avec motif | Autorisé | Auto | 05_tests_statuts |
| TC-04 | Motif obligatoire | SOUMISE → REFUSEE sans motif | Refusé | Auto | 05_tests_statuts |
| TC-05 | Cycle de statut | REFUSEE → BROUILLON | Autorisé | Auto | 05_tests_statuts |
| TC-06 | Transition interdite | BROUILLON → VALIDEE | Refusé | Auto | 05_tests_statuts |
| TC-07 | Transition interdite | VALIDEE → BROUILLON | Refusé | Auto | 05_tests_statuts |
| TC-08 | État final | VALIDEE n'a aucune sortie | Aucune transition | Auto | 05_tests_statuts |
| TC-09 | Rôles | Validation réservée à ADMIN | Rôle ADMIN requis | Auto | 05_tests_statuts |
| TC-10 | Rôles | Soumission réservée à ETUDIANT | Rôle ETUDIANT requis | Auto | 05_tests_statuts |
| TC-11 | Authentification | Connexion avec bon mot de passe | 200 + session | Manuel | 01_login |
| TC-12 | Authentification | Connexion avec mauvais mot de passe | 401 « Identifiants incorrects » | Manuel | 01_login |
| TC-13 | Autorisation | Étudiant appelle `/api/stats` | 403 | Manuel | à produire |
| TC-14 | Autorisation | Étudiant tente de valider une demande | 403, données inchangées | Manuel | à produire |
| TC-15 | CSRF | POST sans en-tête `X-CSRF-Token` | 403 | Manuel | à produire |
| TC-16 | Validation | Créer une demande avec une entreprise inexistante | 400 « Entreprise introuvable » | Manuel | à produire |
| TC-17 | Demandes | Étudiant crée puis soumet une demande | Statut SOUMISE conservé en base et date_depot renseignée | Manuel | 02_etudiant_demande |
| TC-18 | Historique | Chaque changement de statut | Ligne ajoutée dans `historique` | Manuel | 07_comptage_sql |
| TC-19 | Affectation | Affecter un encadreur à une demande non validée | 422 | Manuel | à produire |
| TC-20 | Déploiement | `GET /api/sante` | `{"statut":"ok","base":"ok"}` | Manuel | 06_api_sante |
