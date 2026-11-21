# Fiche de revue de code (modèle)

Dépôt relu : ........................   Groupe relecteur : ........................

| # | Remarque | Fichier/ligne | Gravité | Priorité |
|---|----------|---------------|---------|----------|
| 1 | Requête SQL concaténée (pas de requête préparée) | | Critique | 1 |
| 2 | Pas de contrôle de propriété du dossier | | Critique | 2 |
| 3 | Absence de jeton CSRF sur les formulaires POST | | Haute | 3 |
| 4 | Identifiants BD en dur dans le code | | Moyenne | 4 |
| 5 | Sortie non échappée (htmlspecialchars) | | Moyenne | 5 |
| 6 | Fonction trop longue, noms peu explicites | | Faible | 6 |

Classement : exposition des données > actions à l'insu de l'utilisateur > configuration > lisibilité.
Remplacez par vos 4 vraies remarques et justifiez l'ordre en une phrase chacune.
