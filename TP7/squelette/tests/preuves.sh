#!/usr/bin/env bash
# À lancer depuis TP7/squelette, serveur démarré :
#   php -S localhost:8000 -t public public/index.php
# Usage : ./tests/preuves.sh
BASE=http://localhost:8000/api/demandes
DEM=${TOKEN_DEMANDEUR:-token-demandeur}
VAL=${TOKEN_VALIDEUR:-token-valideur}
DB=${DB:-tp7}
OUT=../preuves
mkdir -p "$OUT"

echo "== 401 : sans jeton =="
curl -i -s -X POST $BASE -H "Content-Type: application/json" \
  -d '{"titre":"Test","description":"abc"}' | tee $OUT/01_erreur_401.txt

echo; echo "== 400 : données invalides =="
curl -i -s -X POST $BASE -H "Authorization: Bearer $DEM" -H "Content-Type: application/json" \
  -d '{"titre":"","description":""}' | tee $OUT/02_erreur_400.txt

echo; echo "== 500 : arrêt de la base =="
sudo systemctl stop mysql 2>/dev/null || sudo systemctl stop mariadb
curl -i -s -X POST $BASE -H "Authorization: Bearer $DEM" -H "Content-Type: application/json" \
  -d '{"titre":"Test","description":"abc"}' | tee $OUT/03_erreur_500.txt
sudo systemctl start mysql 2>/dev/null || sudo systemctl start mariadb
sleep 3

echo; echo "== Balises HTML =="
curl -s -X POST $BASE -H "Authorization: Bearer $DEM" -H "Content-Type: application/json" \
  -d '{"titre":"<b>gras</b> <script>alert(1)</script>","description":"<img src=x onerror=alert(1)>"}' > /dev/null
curl -s -H "Authorization: Bearer $VAL" $BASE | tee $OUT/04_xss_reponse_api.json
echo; grep -n "innerHTML" public/web/app.js | tee $OUT/05_grep_innerhtml.txt
echo "(05 vide = aucun innerHTML)"

echo; echo "== Scénario complet =="
curl -s -X POST $BASE -H "Authorization: Bearer $DEM" -H "Content-Type: application/json" \
  -d '{"titre":"Scénario TP7","description":"Test complet"}' | tee $OUT/06_creation.json
echo
curl -s -H "Authorization: Bearer $VAL" $BASE | tee $OUT/07_liste_avant.json
echo
read -p "ID de la demande à valider : " ID
curl -s -X POST $BASE/$ID/valider -H "Authorization: Bearer $VAL" | tee $OUT/08_validation.json
echo
mysql -u root -p -e "SELECT id,titre,statut FROM demandes;" $DB | tee $OUT/09_persistance_bdd.txt
curl -s -H "Authorization: Bearer $VAL" $BASE | tee $OUT/10_liste_apres.json
echo; echo "Terminé. Ajoutez vos captures d'écran dans ../preuves/"
