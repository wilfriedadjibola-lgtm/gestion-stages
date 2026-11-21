# Gestion des stages

Projet informatique 2 — L3 SIL. Application de dépôt, de suivi et de traitement des demandes de stage des étudiants.

Dépôt : https://github.com/wilfriedadjibola-lgtm/gestion-stages

## Équipe

FALANAN Wilfried · IDRISSOU Nawafou · AMOUSSOU Hyppolite · CHABI EGBA Patrick

## Fonctionnalités

- Authentification (mots de passe hachés, sessions, protection CSRF, comptes actifs uniquement) et rôles : `ETUDIANT`, `ENCADREUR`, `RESPONSABLE`, `ADMIN` (`RESPONSABLE` et `ADMIN` valident, refusent et affectent)
- L'étudiant crée une demande pour une entreprise, la soumet, et peut la reprendre en brouillon si elle est refusée
- L'administrateur valide ou refuse (motif obligatoire) puis affecte un encadreur
- L'encadreur consulte les dossiers qui lui sont affectés
- Historique de chaque changement de statut
- Tableau de bord administrateur (demandes par statut, dossiers sans encadreur)
- Route de santé `/api/sante`

## Cycle d'une demande

`BROUILLON → SOUMISE → VALIDEE` ou `SOUMISE → REFUSEE → BROUILLON`. Toute autre transition est interdite (`VALIDEE` est un état final).

## Structure

```
config/     configuration PHP et scripts SQL (base, schéma, comptage)
Preuves/    captures et preuves, liste de contrôle des 8 éléments
Public/     point d'entrée web (index.php) et interface (HTML, CSS, JS)
src/        API PHP/PDO, règles métier, données de démonstration, tests
TABLEAU_CAS.md   cas de test
```

## Installation

Prérequis : PHP 8.1+ avec `pdo_mysql`, MySQL ou MariaDB, Git.

```powershell
git clone https://github.com/wilfriedadjibola-lgtm/gestion-stages.git
cd gestion-stages
Copy-Item .env.example .env          # puis renseigner DB_PASS
# PowerShell ne supporte pas « < » : envoyer les fichiers avec Get-Content
$mysql = "C:\Program Files\MySQL\MySQL Server 26.7\bin\mysql.exe"
Get-Content config/sql/01_creation_base_et_utilisateur.sql -Raw | & $mysql -u root -p
Get-Content config/sql/02_schema.sql -Raw | & $mysql -u root -p
php src/seed.php                      # données fictives
php src/tests/test_connexion.php
php -S localhost:8000 -t Public Public/index.php
```

Ouvrir http://localhost:8000 — comptes de démonstration (mot de passe `Demo2026!`) :
`etudiant@demo.test`, `admin@demo.test`, `encadreur@demo.test`.

Vérification : http://localhost:8000/api/sante

Le schéma utilise `CREATE TABLE IF NOT EXISTS` : il ne modifie pas une base `projet_sil_tp9` déjà existante (par exemple celle du TP9).

Ne jamais publier `.env` ni de vrais mots de passe.

## Tests

```powershell
php src/tests/test_statuts.php
```

Les autres cas sont décrits dans `TABLEAU_CAS.md`.

## API

| Méthode | Route | Rôle |
|---|---|---|
| GET | `/api/sante` | public |
| POST | `/api/login`, `/api/logout` | — |
| GET | `/api/moi`, `/api/entreprises` | connecté |
| GET | `/api/demandes` | selon le rôle |
| POST | `/api/demandes` | ETUDIANT |
| POST | `/api/demandes/{id}/transition` | ETUDIANT (soumettre) / ADMIN (valider, refuser) |
| POST | `/api/demandes/{id}/encadreur` | ADMIN |
| GET | `/api/encadreurs`, `/api/stats` | ADMIN |

Les requêtes POST exigent l'en-tête `X-CSRF-Token` fourni par `/api/login` et `/api/moi`.

## Sauvegarde et restauration

Script `sauvegarder.ps1` et incident `TICKET-001` : voir le dossier `TP9` du dépôt. La restauration doit toujours se faire sur une base isolée.
