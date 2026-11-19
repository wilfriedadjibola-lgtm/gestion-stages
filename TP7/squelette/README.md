# TP7 - Interface et intégration (squelette)

## Installation
1. `cp .env.example .env` puis adapter DB_USER / DB_PASS
2. `mysql -u root -p < config/schema.sql`   (crée la base tp7 + 2 utilisateurs fictifs)
3. `php -S localhost:8000 -t public public/index.php`
4. Ouvrir http://localhost:8000/web/index.html

Jetons de test : `token-demandeur` et `token-valideur`.

## Preuves
`./tests/preuves.sh` (résultats dans ../preuves), puis ajouter les captures d'écran.
