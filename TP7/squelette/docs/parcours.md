# Parcours des deux rôles

## Rôle 1 : Demandeur (jeton `token-demandeur`)
Écran « Déposer » → saisie titre + description → clic « Envoyer »
→ appel `POST /api/demandes`
→ succès : message vert + demande ajoutée à la liste
→ erreur 400 / 401 / 500 : message lisible sous le formulaire

## Rôle 2 : Valideur (jeton `token-valideur`)
Écran « Liste » → consulte les demandes (statut « en attente »)
→ clic « Valider » → appel `POST /api/demandes/{id}/valider`
→ la liste est rafraîchie, statut « validée »
→ un demandeur qui tente de valider reçoit une erreur 403
