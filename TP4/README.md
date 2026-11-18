# Projet informatique 2 – API (TP4)

Squelette technique d'une API REST en PHP 8 / PDO / MySQL.

## Prérequis
- PHP >= 8.0 avec l'extension `pdo_mysql`
- MySQL ou MariaDB
- Git

## Arborescence
```
public/            point d'entrée (index.php)
src/Controllers/   reçoit la requête, renvoie la réponse HTTP
src/Services/      logique métier
src/Repositories/  accès aux données (PDO)
config/            configuration (lecture du .env)
tests/             tests
docs/              contrat d'API et ADR
```

## Installation
```bash
git clone <url-du-depot> && cd <dossier>
cp .env.example .env        # puis renseigner vos identifiants (jamais commité)
mysql -u root -p -e "CREATE DATABASE projet_sil CHARACTER SET utf8mb4;"
```

## Lancement
```bash
php -S localhost:8000 -t public
curl -i http://localhost:8000/api/sante
```

## Test de connexion
```bash
php tests/connexion_test.php
```

## Documentation
- Contrat des routes : `docs/contrat-api.md`
- Décision d'architecture : `docs/ADR-001.md`

## Équipe
Noms des membres du groupe : _à compléter_
