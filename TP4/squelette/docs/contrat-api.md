# Contrat de l’API TP4/TP8

Toutes les réponses sont au format JSON UTF-8. Les routes utilisant une session renvoient un cookie PHP. Après connexion, transmettre le champ `jeton_csrf` reçu en réponse dans l’en-tête `X-CSRF-Token` à chaque requête POST protégée.

| Méthode et chemin | Corps / paramètres | Codes principaux |
|---|---|---|
| POST `/api/connexion` | `email`, `mot_de_passe` | 200 connecté; 400 données invalides; 401 identifiants incorrects; 503 base indisponible |
| GET `/api/tableau-de-bord` | Cookie de session | 200 utilisateur connecté; 401 non connecté |
| POST `/api/deconnexion` | En-tête `X-CSRF-Token` | 200 déconnecté; 403 jeton CSRF invalide |
| GET `/api/sante` | Aucun | 200 base joignable; 503 base indisponible |
| GET `/api/stages` | Aucun | 200 liste des entreprises (lieux de stage); 500 erreur serveur |
| POST `/api/demandes` | `etudiant_id`, `stage_id` (alias de `entreprise_id`) | 201 créée; 400 données invalides; 401 non connecté; 403 étudiant incorrect ou CSRF; 404 lieu introuvable |
| GET `/api/demandes/{id}` | Cookie de session | 200 demande; 403 demande d’un autre étudiant; 404 inexistante |
| POST `/api/demandes/{id}/valider` | `date_decision` facultative (`AAAA-MM-JJ`) + CSRF | 200 validée; 400 date invalide; 403 accès/CSRF; 404 inexistante; 409 déjà traitée |
| POST `/api/demandes/{id}/refuser` | `motif` requis, `date_decision` facultative + CSRF | 200 refusée; 400 motif/date invalide; 403 accès/CSRF; 404 inexistante; 409 déjà traitée |

## Modèle de données

Le schéma MySQL TP4 ne contient pas de table `stage`. Le champ TP8 `stage_id` est donc accepté comme alias de `entreprise_id` et référence une ligne de `entreprise`. La création de demande exige une session étudiant et vérifie que `etudiant_id` correspond au profil de cette session.

Les décisions sont réservées aux rôles `ENCADREUR`, `SECRETARIAT`, `RESPONSABLE` et `ADMIN`. Une demande déjà `VALIDEE` ou `REFUSEE` ne peut pas être traitée une seconde fois. Les décisions et leur historique sont écrits dans une transaction.
