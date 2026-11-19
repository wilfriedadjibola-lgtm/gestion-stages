
# TP6 - Gestion des demandes de stage

## Présentation

Application web de gestion des demandes de stage.

Le projet utilise PHP, MySQL, HTML, CSS et JavaScript.

## Technologies

- PHP 8.0
- MySQL
- PDO
- HTML5
- CSS3
- JavaScript
- XAMPP
- Postman

## Installation

1. Installer XAMPP.
2. Démarrer Apache et MySQL.
3. Vérifier que la base tp6_api_demandes existe.
4. Placer le dossier du projet dans htdocs.
5. Vérifier la configuration de config/database.php.
6. Ouvrir l'interface dans le navigateur.

## Adresse de l'application

http://localhost/tp6_api_demandes/public/interface.html

## Adresse de l'API

http://localhost/tp6_api_demandes/public/

## Comptes de démonstration

Etudiant :
etudiant@test.local
Etudiant123!

Responsable :
responsable@test.local
Responsable123!

## Fonctionnalités

- Authentification par session PHP.
- Gestion des rôles.
- Création des demandes.
- Consultation des demandes.
- Soumission.
- Validation.
- Refus avec motif obligatoire.
- Retour de REFUSEE vers BROUILLON.
- Historique des changements.
- Transactions SQL.
- Gestion des erreurs HTTP.

## Règles métier

BROUILLON -> SOUMISE

SOUMISE -> VALIDEE

SOUMISE -> REFUSEE

REFUSEE -> BROUILLON

Les autres transitions sont interdites.

## Sécurité

- Requêtes préparées PDO.
- Vérification des mots de passe avec password_verify.
- Régénération de l'identifiant de session.
- Contrôle des rôles.
- Vérification des autorisations.
- Transactions SQL avec rollback.
- Protection des sessions par cookies HttpOnly et SameSite.

## Auteur

Projet universitaire - Licence Systèmes Informatiques et Logiciels.
