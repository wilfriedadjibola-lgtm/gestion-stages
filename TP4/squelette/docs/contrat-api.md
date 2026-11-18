# Contrat des trois routes API

Format JSON (UTF-8), préfixe `/api`.

| Méthode et chemin | Corps / paramètres | Codes de réponse |
|---|---|---|
| GET /api/sante | Aucun | 200 : base joignable ; 503 : base indisponible |
| GET /api/stages | Aucun | 200 : liste (vide au départ) ; 500 : erreur serveur |
| POST /api/demandes | JSON : `etudiant_id`, `stage_id` | 201 : créée (à implémenter) ; 400 : données invalides ; 500 : erreur serveur |

Dans ce TP, seule GET /api/sante est complète. POST /api/demandes valide le corps (400) et répond 501 tant que la création n'est pas implémentée.
