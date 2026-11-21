# TP10 — Scénario de démonstration de 8 minutes

## Objectif

Présenter le projet Gestion des stages, son fonctionnement, ses règles métier, ses contrôles de sécurité et sa procédure de déploiement.

Durée totale : 8 minutes.

| Minute | Présentateur | Démonstration | Preuve à montrer |
|---|---|---|---|
| 0 à 1 | Wilfried | Présenter le besoin, les objectifs et les utilisateurs du projet. | Fiche de cadrage TP1 et exigences TP2 |
| 1 à 2 | Wilfried | Présenter l'architecture PHP, la base MySQL et l'organisation du dépôt. | Dossiers TP3 et TP4, README |
| 2 à 3 | Nawafou | Présenter l'authentification et les rôles. Expliquer la protection des accès. | AuthController.php, AuthService.php et preuves disponibles |
| 3 à 4 | Nawafou | Présenter le parcours de création et de consultation d'une demande. | Code DemandeController.php et preuves TP6/TP7 disponibles |
| 4 à 5 | Hyppolite | Expliquer les règles de changement de statut et l'historique. | Règles métier documentées et schéma SQL TP3 |
| 5 à 6 | Hyppolite | Présenter les contrôles de sécurité et les tests. | Matrices TP8 et captures de tests TP6/TP7 |
| 6 à 7 | Patrick | Présenter le lancement de l'application et le test de santé. | Capture de connexion PHP et capture API santé TP9 |
| 7 à 8 | Patrick | Présenter la sauvegarde, la restauration et conclure. | Script TP9, ticket TICKET-001 et résultats de restauration |

## Déroulement détaillé

### Minute 0–1 : Présentation
Expliquer le problème : le suivi manuel des demandes de stage peut rendre le traitement difficile. Présenter la solution proposée.

### Minute 1–2 : Architecture
Montrer l'organisation du projet, le rôle de PHP, de PDO et de MySQL.

### Minute 2–3 : Authentification
Présenter le contrôle de connexion, les sessions et la séparation des rôles.

### Minute 3–4 : Demandes
Montrer le parcours d'une demande, de sa création à sa consultation. Utiliser des données fictives.

### Minute 4–5 : Règles métier
Expliquer les transitions autorisées :
- BROUILLON vers SOUMISE
- SOUMISE vers VALIDEE
- SOUMISE vers REFUSEE
- REFUSEE vers BROUILLON

Expliquer que les autres transitions sont interdites.

### Minute 5–6 : Sécurité et tests
Présenter les requêtes préparées PDO, la vérification des mots de passe, les autorisations, la protection CSRF et les tests disponibles.

### Minute 6–7 : Déploiement
Montrer la configuration d'exemple, le test de connexion et la réponse de l'API `/api/sante`.

### Minute 7–8 : Sauvegarde et conclusion
Présenter le script de sauvegarde, le ticket d'incident et les résultats de restauration.

## Répétition avec chronomètre

- Faire une répétition complète de 8 minutes.
- Chronométrer chaque partie.
- Éviter les longues lectures de code.
- Préparer les fichiers et captures avant le passage.
- Vérifier le serveur et la base de données avant la soutenance.

## Solution de repli

Si l'application ne démarre pas :
1. Ne pas perdre du temps à réinstaller les outils pendant la soutenance.
2. Présenter les captures d'écran déjà disponibles.
3. Montrer les fichiers sources et les scripts SQL.
4. Présenter le résultat du test de santé enregistré.
5. Expliquer clairement que la démonstration en direct a rencontré un problème technique.

Si la connexion MySQL échoue :
- Présenter le schéma SQL et les requêtes de vérification.
- Utiliser les résultats documentés du TP9.

Si Internet est indisponible :
- Utiliser le dépôt local et les documents enregistrés.
- Ne pas dépendre de GitHub pour la présentation.

## Matériel à préparer

- Ordinateur et chargeur
- Serveur PHP
- Serveur MySQL
- Navigateur
- Éditeur de code
- Captures d'écran
- Dossier de livraison local
- Chronomètre
