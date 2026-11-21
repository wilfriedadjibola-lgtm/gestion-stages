# TP10 — Préparation des questions de soutenance

Chaque membre doit pouvoir expliquer une règle métier, une requête SQL, un test et un commit.

Les répartitions ci-dessous sont des propositions de préparation orale. Chaque membre doit vérifier les éléments et les commits auxquels il a réellement contribué.

## 1. FALANAN Wilfried — Architecture et base de données

### Règle métier
Une demande de stage suit un cycle de traitement défini. Elle ne doit pas passer directement d'un état quelconque à un autre.

### Requête SQL
Exemple de consultation du nombre de demandes par statut :

```sql
SELECT statut, COUNT(*) AS nombre
FROM demande_stage
GROUP BY statut;
```

Cette requête regroupe les demandes selon leur statut et compte les demandes de chaque groupe.

### Test à expliquer
Le test de connexion PDO permet de vérifier que l'application arrive à communiquer avec MySQL.

Preuve disponible : `TP9/preuves/preuve_03_connextion_php.png`.

### Commit
Commit de livraison TP9 :
`b45b527` — Ajout du TP9 : déploiement et sauvegarde.

Il s'agit du commit du dépôt. Ne pas le présenter comme une contribution personnelle sans en vérifier l'auteur.

## 2. IDRISSOU Nawafou — Authentification et autorisations

### Règle métier
Un utilisateur ne peut accéder qu'aux opérations autorisées par son rôle.

### Requête SQL
Exemple de recherche d'un utilisateur par adresse électronique :

```sql
SELECT id, email, mot_de_passe_hash, role
FROM utilisateur
WHERE email = ?;
```

Le paramètre doit être transmis avec une requête préparée PDO.

Vérifier les noms exacts des colonnes dans le schéma avant la démonstration.

### Test à expliquer
Présenter un test de connexion valide et un cas d'accès non autorisé, avec la preuve correspondante lorsqu'elle est disponible.

### Commit
Retrouver dans Git l'identifiant du commit réellement réalisé par ce membre. Expliquer son objectif et les fichiers concernés.

## 3. AMOUSSOU Hyppolite — Demandes et règles métier

### Règle métier
Une demande SOUMISE peut être VALIDEE ou REFUSEE. Un refus doit être accompagné d'un motif obligatoire, conformément aux règles du projet.

### Requête SQL
Exemple de consultation des demandes :

```sql
SELECT *
FROM demande_stage
ORDER BY id DESC;
```

Cette requête affiche les demandes par ordre décroissant d'identifiant.

### Test à expliquer
Présenter un test de création ou de validation d'une demande, puis vérifier que le résultat est conservé.

### Commit
Retrouver le commit réellement réalisé par ce membre et expliquer la modification apportée.

## 4. CHABI EGBA Patrick — Tests et déploiement

### Règle métier
Une opération non autorisée ne doit pas modifier les données. Une demande doit respecter les transitions de statut prévues.

### Requête SQL
Exemple de comptage des demandes :

```sql
SELECT COUNT(*) AS nombre_demandes
FROM demande_stage;
```

### Test à expliquer
La vérification de santé de l'application confirme que l'API répond et que la base de données est joignable.

Preuve disponible : `TP9/preuves/preuve_05_api_sante.png`.

### Commit
Retrouver le commit réellement réalisé par ce membre et expliquer son objectif.

## Questions complémentaires possibles

### Pourquoi utiliser PDO ?
PDO permet à PHP de communiquer avec la base de données et d'utiliser des requêtes préparées.

### Pourquoi utiliser les requêtes préparées ?
Elles séparent les instructions SQL des données fournies et contribuent à prévenir les injections SQL.

### Pourquoi conserver un historique ?
Pour retrouver les changements effectués sur les demandes et faciliter le suivi.

### Pourquoi sauvegarder la base ?
Pour pouvoir récupérer les données après une panne ou un incident.

### Pourquoi utiliser Git ?
Pour conserver l'historique des modifications, suivre les versions et collaborer.

### Que faire si une démonstration échoue ?
Présenter la solution de repli, les captures et les résultats réellement obtenus, puis expliquer le problème technique.
