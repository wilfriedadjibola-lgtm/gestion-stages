-- 002_seed.sql — jeu d'essai entièrement fictif
SET NAMES utf8mb4;

INSERT INTO utilisateur (id,email,mot_de_passe_hash,role,nom,prenom,actif) VALUES
(1,'koffi.adjovi@univ.test','$2y$10$w8vj0PJ5HJkVug46z0gCgeQbKFAUvmEMRLbgCLqNUQ5VDvoiPCIXm','ETUDIANT','ADJOVI','Koffi',1),
(2,'aicha.bello@univ.test','$2y$10$w8vj0PJ5HJkVug46z0gCgeQbKFAUvmEMRLbgCLqNUQ5VDvoiPCIXm','ETUDIANT','BELLO','Aicha',1),
(3,'marc.dossou@univ.test','$2y$10$w8vj0PJ5HJkVug46z0gCgeQbKFAUvmEMRLbgCLqNUQ5VDvoiPCIXm','ETUDIANT','DOSSOU','Marc',1),
(4,'secretariat@univ.test','$2y$10$w8vj0PJ5HJkVug46z0gCgeQbKFAUvmEMRLbgCLqNUQ5VDvoiPCIXm','SECRETARIAT','AGOSSOU','Rita',1),
(5,'resp.pedago@univ.test','$2y$10$w8vj0PJ5HJkVug46z0gCgeQbKFAUvmEMRLbgCLqNUQ5VDvoiPCIXm','RESPONSABLE','TOSSOU','Bertin',1),
(6,'lea.hounkpati@univ.test','$2y$10$w8vj0PJ5HJkVug46z0gCgeQbKFAUvmEMRLbgCLqNUQ5VDvoiPCIXm','ENCADREUR','HOUNKPATI','Léa',1),
(7,'paul.sossou@univ.test','$2y$10$w8vj0PJ5HJkVug46z0gCgeQbKFAUvmEMRLbgCLqNUQ5VDvoiPCIXm','ENCADREUR','SOSSOU','Paul',1),
(8,'admin@univ.test','$2y$10$w8vj0PJ5HJkVug46z0gCgeQbKFAUvmEMRLbgCLqNUQ5VDvoiPCIXm','ADMIN','GBAGUIDI','Noël',1);

INSERT INTO etudiant (id,utilisateur_id,numero_etudiant,filiere) VALUES
(1,1,'ETU-2026-001','SIL'),(2,2,'ETU-2026-002','SIL'),(3,3,'ETU-2026-003','SIL');

INSERT INTO encadreur (id,utilisateur_id,grade) VALUES
(1,6,'Maître de conférences'),(2,7,'Assistant');

INSERT INTO entreprise (id,nom,ville,contact) VALUES
(1,'Atelier Numerique','Porto-Novo','contact@atelier-numerique.test'),
(2,'Reseau Plus','Cotonou','rh@reseau-plus.test');

INSERT INTO demande_stage
(id,etudiant_id,entreprise_id,encadreur_id,statut,motif_refus,date_depot,date_decision) VALUES
(1,1,1,NULL,'BROUILLON',NULL,NULL,NULL),
(2,2,1,NULL,'SOUMISE',NULL,'2026-11-02 10:00:00',NULL),
(3,3,2,1,'VALIDEE',NULL,'2026-10-20 09:15:00','2026-10-27 16:40:00'),
(4,1,2,NULL,'REFUSEE','Convention incomplète : signature de l''entreprise absente',
 '2026-10-05 11:00:00','2026-10-09 14:20:00');

INSERT INTO historique
(demande_id,ancien_statut,nouveau_statut,auteur_id,motif,date_evenement) VALUES
(2,NULL,'SOUMISE',2,NULL,'2026-11-02 10:00:00'),
(3,NULL,'SOUMISE',3,NULL,'2026-10-20 09:15:00'),
(3,'SOUMISE','VALIDEE',5,NULL,'2026-10-27 16:40:00'),
(4,NULL,'SOUMISE',1,NULL,'2026-10-05 11:00:00'),
(4,'SOUMISE','REFUSEE',5,'Convention incomplète','2026-10-09 14:20:00');
