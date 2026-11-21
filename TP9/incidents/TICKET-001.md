# TICKET-001 — Incident de restauration MySQL

## 1. Informations générales

- Projet : Gestion des stages
- Type : Incident de base de données
- Gravité : Moyenne
- Statut : Résolu
- Base source : projet_sil_tp9
- Base de test : projet_sil_tp9_restauration

## 2. Description de l'incident

Lors de la première tentative de restauration, MySQL a affiché :

ERROR 3546 (HY000): @@GLOBAL.GTID_PURGED cannot be changed:
the added gtid set must not overlap with @@GLOBAL.GTID_EXECUTED

La restauration était bloquée par un conflit de GTID.

## 3. Cause identifiée

Le fichier de sauvegarde contenait une instruction GTID_PURGED incompatible avec les GTID déjà exécutés sur le serveur MySQL.

## 4. Solution appliquée

Une nouvelle sauvegarde a été générée avec l'option :

--set-gtid-purged=OFF

La commande utilisée :

mysqldump -u root -p --set-gtid-purged=OFF --single-transaction --routines --triggers projet_sil_tp9

La restauration a ensuite été effectuée dans la base isolée projet_sil_tp9_restauration.

## 5. Simulation de panne

Pour tester la procédure :
- La table historique a été supprimée uniquement dans la base de test.
- La base de test a été recréée.
- La sauvegarde a été restaurée.
- Les tables et les données ont été vérifiées.

La base source projet_sil_tp9 n'a pas été modifiée pendant cette simulation.

## 6. Résultats de vérification

| Table | Nombre d'enregistrements restaurés |
|---|---:|
| utilisateur | 8 |
| etudiant | 3 |
| encadreur | 2 |
| entreprise | 2 |
| demande_stage | 4 |
| historique | 5 |

Les 6 tables et la vue v_dossiers_sans_encadreur sont présents après restauration.

## 7. Conclusion

L'incident est résolu. La sauvegarde et la restauration ont été testées avec succès dans une base isolée.

Les nombres d'enregistrements restaurés correspondent aux nombres de la base source.
