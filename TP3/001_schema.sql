-- Projet informatique 2 — L3 SIL · Application de gestion des stages
-- 001_schema.sql — création du schéma sur une base vide
-- Dialecte : MySQL 8 / MariaDB 11

SET NAMES utf8mb4;

DROP VIEW IF EXISTS v_dossiers_sans_encadreur;
DROP TABLE IF EXISTS historique;
DROP TABLE IF EXISTS demande_stage;
DROP TABLE IF EXISTS encadreur;
DROP TABLE IF EXISTS etudiant;
DROP TABLE IF EXISTS entreprise;
DROP TABLE IF EXISTS utilisateur;

CREATE TABLE utilisateur (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  email VARCHAR(100) NOT NULL,
  mot_de_passe_hash VARCHAR(255) NOT NULL,
  role ENUM('ETUDIANT','ENCADREUR','SECRETARIAT','RESPONSABLE','ADMIN') NOT NULL,
  nom VARCHAR(60) NOT NULL,
  prenom VARCHAR(60) NOT NULL,
  actif TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY uk_utilisateur_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE etudiant (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  utilisateur_id INT UNSIGNED NOT NULL,
  numero_etudiant VARCHAR(20) NOT NULL,
  filiere VARCHAR(60) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_etudiant_utilisateur (utilisateur_id),
  UNIQUE KEY uk_etudiant_numero (numero_etudiant),
  CONSTRAINT fk_etudiant_utilisateur FOREIGN KEY (utilisateur_id)
    REFERENCES utilisateur(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE encadreur (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  utilisateur_id INT UNSIGNED NOT NULL,
  grade VARCHAR(60) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_encadreur_utilisateur (utilisateur_id),
  CONSTRAINT fk_encadreur_utilisateur FOREIGN KEY (utilisateur_id)
    REFERENCES utilisateur(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE entreprise (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nom VARCHAR(80) NOT NULL,
  ville VARCHAR(60) NOT NULL,
  contact VARCHAR(100) NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_entreprise_nom_ville (nom, ville)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE demande_stage (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  etudiant_id INT UNSIGNED NOT NULL,
  entreprise_id INT UNSIGNED NOT NULL,
  encadreur_id INT UNSIGNED NULL,
  statut ENUM('BROUILLON','SOUMISE','VALIDEE','REFUSEE') NOT NULL DEFAULT 'BROUILLON',
  motif_refus VARCHAR(255) NULL,
  date_depot DATETIME NULL,
  date_decision DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_demande_etudiant (etudiant_id),
  KEY idx_demande_entreprise (entreprise_id),
  KEY idx_demande_encadreur (encadreur_id),
  KEY idx_demande_statut (statut),
  CONSTRAINT fk_demande_etudiant FOREIGN KEY (etudiant_id)
    REFERENCES etudiant(id) ON DELETE CASCADE,
  CONSTRAINT fk_demande_entreprise FOREIGN KEY (entreprise_id)
    REFERENCES entreprise(id),
  CONSTRAINT fk_demande_encadreur FOREIGN KEY (encadreur_id)
    REFERENCES encadreur(id) ON DELETE SET NULL,
  CONSTRAINT ck_demande_motif CHECK (statut <> 'REFUSEE' OR motif_refus IS NOT NULL),
  CONSTRAINT ck_demande_decision CHECK (statut IN ('BROUILLON','SOUMISE') OR date_decision IS NOT NULL)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE historique (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  demande_id INT UNSIGNED NOT NULL,
  ancien_statut ENUM('BROUILLON','SOUMISE','VALIDEE','REFUSEE') NULL,
  nouveau_statut ENUM('BROUILLON','SOUMISE','VALIDEE','REFUSEE') NOT NULL,
  auteur_id INT UNSIGNED NOT NULL,
  motif VARCHAR(255) NULL,
  date_evenement DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_historique_demande (demande_id),
  KEY idx_historique_auteur (auteur_id),
  CONSTRAINT fk_historique_demande FOREIGN KEY (demande_id)
    REFERENCES demande_stage(id) ON DELETE CASCADE,
  CONSTRAINT fk_historique_auteur FOREIGN KEY (auteur_id)
    REFERENCES utilisateur(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE OR REPLACE VIEW v_dossiers_sans_encadreur AS
SELECT d.id AS demande_id, u.nom AS etudiant_nom,
       u.prenom AS etudiant_prenom, d.date_decision AS date_decision
FROM demande_stage d
JOIN etudiant e ON e.id = d.etudiant_id
JOIN utilisateur u ON u.id = e.utilisateur_id
WHERE d.statut = 'VALIDEE' AND d.encadreur_id IS NULL;
