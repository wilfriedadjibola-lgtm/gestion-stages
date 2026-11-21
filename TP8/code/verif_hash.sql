SELECT login, LEFT(mot_de_passe, 7) AS prefixe, LENGTH(mot_de_passe) AS longueur FROM users;
-- Attendu : prefixe = $2y$10$ ; longueur = 60
