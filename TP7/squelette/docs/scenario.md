# Scénario de bout en bout

1. Création d'une demande par le demandeur (`preuves/06_creation.json` + capture)
2. La demande est retrouvée dans la liste (`07_liste_avant.json`)
3. Validation par le valideur (`08_validation.json`)
4. Rafraîchissement (F5) : le statut « validée » est conservé
   (`09_persistance_bdd.txt`, `10_liste_apres.json`)

## Captures d'écran à fournir (dossier `preuves/`)
- Erreurs 401, 400, 500 : message lisible sous le formulaire
- Test des balises HTML : texte affiché tel quel, aucune alerte
- Formulaire rempli, demande dans la liste, clic « Valider », F5, statut « validée »
