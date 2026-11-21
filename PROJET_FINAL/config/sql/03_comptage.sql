SELECT 'utilisateur' AS table_, COUNT(*) AS lignes FROM utilisateur
UNION ALL SELECT 'etudiant', COUNT(*) FROM etudiant
UNION ALL SELECT 'encadreur', COUNT(*) FROM encadreur
UNION ALL SELECT 'entreprise', COUNT(*) FROM entreprise
UNION ALL SELECT 'demande_stage', COUNT(*) FROM demande_stage
UNION ALL SELECT 'historique', COUNT(*) FROM historique;

SELECT COUNT(*) AS dossiers_sans_encadreur FROM v_dossiers_sans_encadreur;
