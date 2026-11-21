USE projet_sil_tp9;

CREATE TABLE IF NOT EXISTS utilisateur (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(100) NOT NULL UNIQUE,
  mot_de_passe_hash VARCHAR(255) NOT NULL,
  role ENUM('ETUDIANT','ENCADREUR','SECRETARIAT','RESPONSABLE','ADMIN') NOT NULL,
  nom VARCHAR(60) NOT NULL,
  prenom VARCHAR(60) NOT NULL,
  actif TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS etudiant (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  utilisateur_id INT UNSIGNED NOT NULL UNIQUE,
  numero_etudiant VARCHAR(20) NOT NULL UNIQUE,
  filiere VARCHAR(60) NOT NULL,
  FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS encadreur (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  utilisateur_id INT UNSIGNED NOT NULL UNIQUE,
  grade VARCHAR(60) NULL,
  FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS entreprise (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(80) NOT NULL,
  ville VARCHAR(60) NOT NULL,
  contact VARCHAR(100) NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS demande_stage (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  etudiant_id INT UNSIGNED NOT NULL,
  entreprise_id INT UNSIGNED NOT NULL,
  encadreur_id INT UNSIGNED NULL,
  statut ENUM('BROUILLON','SOUMISE','VALIDEE','REFUSEE') NOT NULL DEFAULT 'BROUILLON',
  motif_refus VARCHAR(255) NULL,
  date_depot DATETIME NULL,
  date_decision DATETIME NULL,
  FOREIGN KEY (etudiant_id) REFERENCES etudiant(id),
  FOREIGN KEY (entreprise_id) REFERENCES entreprise(id),
  FOREIGN KEY (encadreur_id) REFERENCES encadreur(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS historique (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  demande_id INT UNSIGNED NOT NULL,
  ancien_statut ENUM('BROUILLON','SOUMISE','VALIDEE','REFUSEE') NULL,
  nouveau_statut ENUM('BROUILLON','SOUMISE','VALIDEE','REFUSEE') NOT NULL,
  auteur_id INT UNSIGNED NOT NULL,
  motif VARCHAR(255) NULL,
  date_evenement DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (demande_id) REFERENCES demande_stage(id),
  FOREIGN KEY (auteur_id) REFERENCES utilisateur(id)
) ENGINE=InnoDB;

CREATE OR REPLACE VIEW v_dossiers_sans_encadreur AS
SELECT d.id AS demande_id, u.nom AS etudiant_nom, u.prenom AS etudiant_prenom, d.date_decision
FROM demande_stage d
JOIN etudiant e ON e.id = d.etudiant_id
JOIN utilisateur u ON u.id = e.utilisateur_id
WHERE d.statut = 'VALIDEE' AND d.encadreur_id IS NULL;
