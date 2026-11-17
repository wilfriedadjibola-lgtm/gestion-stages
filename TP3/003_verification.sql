-- 003_verification.sql — contrôles après chargement
SELECT 'utilisateur' AS table_, COUNT(*) AS lignes FROM utilisateur
UNION ALL SELECT 'etudiant', COUNT(*) FROM etudiant
UNION ALL SELECT 'encadreur', COUNT(*) FROM encadreur
UNION ALL SELECT 'entreprise', COUNT(*) FROM entreprise
UNION ALL SELECT 'demande_stage', COUNT(*) FROM demande_stage
UNION ALL SELECT 'historique', COUNT(*) FROM historique;

SELECT statut, COUNT(*) AS demandes
FROM demande_stage GROUP BY statut
ORDER BY FIELD(statut,'BROUILLON','SOUMISE','VALIDEE','REFUSEE');

SELECT COUNT(*) AS dossiers_sans_encadreur FROM v_dossiers_sans_encadreur;

SELECT h.id,h.demande_id,h.nouveau_statut,u.email AS auteur
FROM historique h JOIN utilisateur u ON u.id=h.auteur_id ORDER BY h.id;
