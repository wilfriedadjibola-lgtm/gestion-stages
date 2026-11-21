# TP10 — Rétrospective du projet

## 1. Point positif

Le projet a progressivement évolué depuis le cadrage jusqu'au déploiement.

Les travaux ont permis de produire :
- Des documents de cadrage et d'exigences.
- Des modèles UML et un schéma de base de données.
- Une API PHP utilisant PDO.
- Des fonctionnalités de gestion des demandes.
- Des documents et preuves de tests.
- Une procédure de sauvegarde et de restauration.

Le test de santé de l'API et la restauration sur une base isolée ont notamment été réalisés et documentés au TP9.

## 2. Difficulté rencontrée

L'intégration et la configuration de l'environnement ont demandé plusieurs vérifications.

Les difficultés ont notamment concerné :
- La configuration de la connexion MySQL.
- Le paramétrage des fichiers d'environnement.
- La sauvegarde et la restauration avec les options GTID de MySQL.
- La coordination des documents et des preuves dans le dépôt Git.

La difficulté liée à la restauration a été documentée dans `TP9/incidents/TICKET-001.md`.

## 3. Changement proposé pour la version 2

Pour une prochaine version, nous proposons de :
- Unifier les différentes parties de l'application dans une seule version cohérente.
- Améliorer l'interface utilisateur et son ergonomie.
- Ajouter un tableau de bord avec des statistiques.
- Renforcer les tests automatisés.
- Centraliser les journaux et l'historique des opérations.
- Améliorer la documentation d'installation.
- Préparer une démonstration intégrée et répétée avant la livraison.

## 4. Conclusion

Le projet a permis de mettre en pratique les notions de conception, de programmation web, de gestion de base de données, de sécurité, de tests, de déploiement et de travail avec Git.

La soutenance doit permettre de présenter les réalisations, de justifier les choix techniques et d'identifier les améliorations possibles.
