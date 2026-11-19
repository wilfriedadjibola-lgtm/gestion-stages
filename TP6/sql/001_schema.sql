CREATE DATABASE IF NOT EXISTS tp6_api_demandes
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tp6_api_demandes;

CREATE TABLE IF NOT EXISTS utilisateurs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(120) NOT NULL,
  email VARCHAR(180) NOT NULL UNIQUE,
  mot_de_passe VARCHAR(255) NOT NULL,
  role ENUM('ETUDIANT','RESPONSABLE') NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS demande_stage (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  entreprise VARCHAR(180) NOT NULL,
  date_debut DATE NOT NULL,
  statut ENUM('BROUILLON','SOUMISE','VALIDEE','REFUSEE') NOT NULL DEFAULT 'BROUILLON',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_demande_etudiant FOREIGN KEY (student_id)
    REFERENCES utilisateurs(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS historique_demande (
  id INT AUTO_INCREMENT PRIMARY KEY,
  demande_id INT NOT NULL,
  utilisateur_id INT NOT NULL,
  ancien_statut VARCHAR(30) NOT NULL,
  nouveau_statut VARCHAR(30) NOT NULL,
  motif TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_historique_demande FOREIGN KEY (demande_id)
    REFERENCES demande_stage(id),
  CONSTRAINT fk_historique_utilisateur FOREIGN KEY (utilisateur_id)
    REFERENCES utilisateurs(id)
) ENGINE=InnoDB;
