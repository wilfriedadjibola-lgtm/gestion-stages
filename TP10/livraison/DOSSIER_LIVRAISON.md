# TP10 — Dossier de livraison et soutenance
## Projet informatique 2 — L3 SIL
### Projet : Gestion des stages

## 1. Présentation du projet

Le projet Gestion des stages est une application destinée à faciliter le dépôt, le suivi et le traitement des demandes de stage des étudiants.

Technologies utilisées :
- PHP 8 et PDO
- MySQL / MariaDB
- HTML, CSS et JavaScript
- Git et GitHub
- Navigateur web et outils de test

Dépôt GitHub :
https://github.com/wilfriedadjibola-lgtm/gestion-stages

## 2. Composition du dossier de livraison

Le dépôt contient les éléments réalisés au cours des différents travaux pratiques :

| Élément | Emplacement |
|---|---|
| Fiche de cadrage | TP1_Fiche_de_cadrage_Gestion_des_stages.pdf |
| Exigences et traçabilité | TP2_Exigences_et_matrice_de_tracabilite.pdf |
| Modélisation UML et SQL | TP3/ |
| API PHP et authentification | TP4/squelette/ |
| Gestion des demandes | TP6/ |
| Interface et intégration | TP7/ |
| Tests et revue de code | TP8/ |
| Déploiement et sauvegarde | TP9/ |

## 3. Liste de contrôle des huit éléments

Avant la livraison finale, vérifier chaque élément :

- [ ] 1. Présentation du projet : objectif, périmètre et technologies.
- [ ] 2. Cahier des exigences : besoins fonctionnels et matrice de traçabilité.
- [ ] 3. Conception : diagrammes UML et modèle de données.
- [ ] 4. Base de données : schéma SQL, données fictives et vérifications.
- [ ] 5. Application : code source, configuration d'exemple et instructions d'installation.
- [ ] 6. Sécurité et tests : authentification, rôles, contrôles, résultats et preuves.
- [ ] 7. Déploiement : procédure de lancement, configuration et vérification de santé.
- [ ] 8. Sauvegarde et restauration : script, ticket d'incident et résultats de comparaison.

Important : une case ne doit être cochée qu'après vérification effective du document ou de la preuve correspondante.

## 4. Installation et lancement

Prérequis :
- PHP 8 avec PDO et PDO MySQL
- MySQL ou MariaDB
- Git
- Navigateur web

Récupération :
```bash
git clone https://github.com/wilfriedadjibola-lgtm/gestion-stages.git
```

Pour l'environnement TP4 :
```powershell
cd TP4/squelette
Copy-Item config/.env.example .env
```

Renseigner les paramètres de connexion dans `.env`, puis :
```powershell
php tests/test_connexion.php
php -S localhost:8000 -t public public/index.php
```

Vérification :
http://localhost:8000/api/sante

Ne jamais publier le fichier `.env` ni les mots de passe.

## 5. Sauvegarde et restauration

Les procédures sont documentées dans `TP9/README.md`.

Le script de sauvegarde se trouve dans :
`TP9/scripts/sauvegarder.ps1`

Le ticket d'incident est :
`TP9/incidents/TICKET-001.md`

La restauration a été testée sur une base isolée, sans effectuer de suppression dans la base source.

## 6. Équipe du projet

- FALANAN Wilfried
- IDRISSOU Nawafou
- AMOUSSOU Hyppolite
- CHABI EGBA Patrick

## 7. Conclusion

Le dossier rassemble les documents de cadrage, de conception, de développement, de test et de déploiement.

Les éléments de la liste de contrôle doivent être vérifiés avant la remise définitive.
