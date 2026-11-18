# Contrat d'API – v1

Format : JSON (UTF-8). Préfixe : `/api`. Les routes `taches` sont un exemple à adapter au sujet du groupe.

## 1. GET /api/sante
Vérifie que l'API et la base répondent.
- **Corps de requête** : aucun
- **200** : `{"statut":"ok","base":"joignable"}`
- **503** : `{"statut":"erreur","base":"injoignable"}`

## 2. GET /api/taches
Liste les tâches.
- **Corps de requête** : aucun
- **200** : `[{"id":1,"titre":"Écrire le README","fait":false}]`
- **500** : `{"erreur":"Erreur interne"}`

## 3. POST /api/taches
Crée une tâche.
- **Corps de requête** : `{"titre":"Écrire le README"}` (titre obligatoire, 1 à 120 caractères)
- **201** : `{"id":2,"titre":"Écrire le README","fait":false}`
- **400** : `{"erreur":"JSON invalide"}`
- **422** : `{"erreur":"Le champ titre est obligatoire"}`
- **500** : `{"erreur":"Erreur interne"}`

## Erreurs communes
- **404** : `{"erreur":"Route introuvable"}`
- **405** : `{"erreur":"Méthode non autorisée"}`
