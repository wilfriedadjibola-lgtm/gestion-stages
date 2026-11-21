# Gestion des stages — TP4

API PHP 8.4 avec PDO et MySQL/MariaDB. Le schéma existant utilise `entreprise` comme lieu de stage et `demande_stage` pour les demandes.

## Prérequis

- PHP 8.4 avec `pdo_mysql`
- MySQL ou MariaDB avec le schéma TP4 chargé
- Git

## Configuration et lancement

Si `.env` n’existe pas, le créer à partir de `config/.env.example`, puis renseigner un compte MySQL applicatif à privilèges limités. Le fichier `.env` est ignoré par Git.

```powershell
if (!(Test-Path .env)) { Copy-Item config\.env.example .env }
php tests/test_connexion.php
php -S localhost:8000 -t public public/index.php
```

## Routes principales

- `POST /api/connexion` : JSON `email`, `mot_de_passe`; réponse avec cookie de session et `jeton_csrf`.
- `GET /api/tableau-de-bord` : vérifie la session.
- `GET /api/sante` : vérifie la connexion MySQL.
- `GET /api/stages` : liste les entreprises qui servent de lieux de stage.
- `POST /api/demandes` : requiert une session étudiant, `X-CSRF-Token`, `etudiant_id` et `stage_id` (alias de `entreprise_id`).
- `GET /api/demandes/{id}` : lit une demande; un étudiant ne peut lire que la sienne.
- `POST /api/demandes/{id}/valider` ou `/refuser` : décision réservée aux rôles habilités et protégée par CSRF.
- `POST /api/deconnexion` : termine la session; requiert le jeton CSRF.

Les corps POST sont JSON. Le contrat détaillé, les statuts et les codes d’erreur sont dans [`docs/contrat-api.md`](docs/contrat-api.md).
