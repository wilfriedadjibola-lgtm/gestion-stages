-- Données fictives uniquement
CREATE DATABASE IF NOT EXISTS tp7 CHARACTER SET utf8mb4;
USE tp7;

CREATE TABLE IF NOT EXISTS utilisateurs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(50) NOT NULL,
  role ENUM('demandeur','valideur') NOT NULL,
  token VARCHAR(64) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS demandes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titre VARCHAR(100) NOT NULL,
  description TEXT NOT NULL,
  statut VARCHAR(20) NOT NULL DEFAULT 'en attente',
  utilisateur_id INT NOT NULL,
  cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

INSERT IGNORE INTO utilisateurs (id, nom, role, token) VALUES
  (1, 'Alice (demandeur)', 'demandeur', 'token-demandeur'),
  (2, 'Bob (valideur)',    'valideur',  'token-valideur');
