# TP9 — Déploiement et sauvegarde

## 1. Objectif
Déployer le projet de gestion des stages et tester la sauvegarde et la restauration.

## 2. Prérequis
- Git
- PHP 8.4
- MySQL Server
- Extensions PDO et PDO MySQL

## 3. Récupération du projet
git clone https://github.com/wilfriedadjibola-lgtm/gestion-stages.git

## 4. Création de la base et de l'utilisateur
Exécuter le fichier sql/01_creation_base_et_utilisateur.sql avec le client MySQL administrateur.
Remplacer CHANGE_ME par un mot de passe personnel avant l'exécution.

## 5. Import du schéma et des données
Dans MySQL, sélectionner projet_sil_tp9 puis importer TP3/001_schema.sql et TP3/002_seed.sql.

## 6. Configuration de l'application
Configurer TP4/squelette/.env avec DB_HOST=127.0.0.1, DB_PORT=3306, DB_NAME=projet_sil_tp9, DB_USER=tp9_app et le mot de passe choisi.

Ne jamais publier le fichier .env.

## 7. Test de connexion
Depuis TP4/squelette, exécuter php tests/test_connexion.php.
Puis démarrer avec php -S localhost:8000 -t public public/index.php.
Tester http://localhost:8000/api/sante.

## 8. Sauvegarde
Depuis TP9, exécuter .\scripts\sauvegarder.ps1.
Le fichier SQL sera enregistré dans preuves.

## 9. Vérification
Dans MySQL, exécuter TP9/sql/02_comptage.sql et conserver les résultats.

## 10. Test de restauration
Sur la base de test uniquement, relever les nombres de lignes, effectuer le test de suppression, restaurer la sauvegarde et comparer les résultats.

## 11. Ticket d'incident
Consulter incidents/TICKET-001.md.

## 12. Sécurité
- Ne pas utiliser root dans l'application.
- Ne pas publier les mots de passe.
- Ne jamais effectuer de test destructif sur une base de production.

## 9. Résultats des tests de sauvegarde et restauration

### Sauvegarde

- Base sauvegardée : `projet_sil_tp9`
- Fichier : `preuves/restauration_test.sql`
- Option GTID : `--set-gtid-purged=OFF`
- Résultat : sauvegarde créée sans erreur.

### Restauration

La sauvegarde a été restaurée dans une base isolée :

`projet_sil_tp9_restauration`

Les objets suivants ont été retrouvés :
- utilisateur
- etudiant
- encadreur
- entreprise
- demande_stage
- historique
- v_dossiers_sans_encadreur (vue)

### Comparaison des données

| Table | Base source | Base restaurée |
|---|---:|---:|
| utilisateur | 8 | 8 |
| etudiant | 3 | 3 |
| encadreur | 2 | 2 |
| entreprise | 2 | 2 |
| demande_stage | 4 | 4 |
| historique | 5 | 5 |

### Simulation d'incident

La table `historique` a été supprimée uniquement dans la base de test. La base de test a ensuite été recréée et restaurée à partir de la sauvegarde.

Résultat : les 6 tables et la vue sont présents, et les données ont été récupérées.

**Conclusion :** les tests de sauvegarde, de restauration et de récupération après incident sont validés.
