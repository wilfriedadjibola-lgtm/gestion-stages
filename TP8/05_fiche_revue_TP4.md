# Fiche de revue de code – TP4

Dépôt relu : depot_gestion/TP4/squelette
Groupe relecteur : À compléter

| # | Remarque | Fichier concerné | Gravité | Priorité |
|---|---|---|---|---|
| 1 | Aucun mécanisme d'authentification et d'autorisation n'est présent dans le squelette examiné. | public/index.php | Haute | 1 |
| 2 | La création réelle des demandes n'est pas encore implémentée : la route renvoie HTTP 501. | src/Controllers/DemandeController.php | Haute | 2 |
| 3 | La validation des identifiants vérifie leur type entier, mais pas leur valeur positive ni leur existence en base. | src/Controllers/DemandeController.php | Moyenne | 3 |
| 4 | La liste des stages est codée en dur et ne récupère pas les données depuis MySQL. | src/Controllers/StageController.php | Moyenne | 4 |

## Justification du classement

1. L'absence d'authentification et d'autorisation peut exposer les futures fonctionnalités protégées.
2. L'absence de création empêche l'utilisation de la fonctionnalité principale de dépôt des demandes.
3. Une validation plus complète des identifiants est nécessaire avant leur utilisation métier.
4. La consultation des stages doit être reliée aux données réelles de la base.

Remarque : ces constats décrivent l'état du squelette TP4 et non une attaque effectivement réalisée.
