# Gestion des stages – Squelette technique (TP4)

## Prérequis
- PHP >= 8.0 avec l'extension `pdo_mysql`
- MySQL ou MariaDB (XAMPP convient)
- Git

## Arborescence
```
public/            point d'entrée (index.php)
src/Controllers/   contrôleurs HTTP
src/Services/      logique métier
src/Repositories/  accès aux données (PDO)
config/            configuration (.env.example, config.php)
tests/             test de connexion
docs/              contrat d'API et ADR-001
```

## Installation
```bash
copy config\.env.example .env     # Windows  (Linux/Mac : cp config/.env.example .env)
mysql -u root -e "CREATE DATABASE projet_sil CHARACTER SET utf8mb4;"
```

## Test de connexion
```bash
php tests/test_connexion.php
```

## Lancement
```bash
php -S localhost:8000 -t public
```
Puis ouvrir http://localhost:8000/api/sante
