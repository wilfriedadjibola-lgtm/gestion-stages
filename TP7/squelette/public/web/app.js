const API = '/api/demandes';
const form = document.getElementById('form-depot');
const erreur = document.getElementById('erreur');
const succes = document.getElementById('succes');
const liste = document.getElementById('liste');

function headers() {
  const h = { 'Content-Type': 'application/json' };
  const t = document.getElementById('token').value.trim();
  if (t) h['Authorization'] = 'Bearer ' + t;
  return h;
}

function messageErreur(status) {
  if (status === 400) return "Erreur 400 : données invalides, vérifiez le titre et la description.";
  if (status === 401) return "Erreur 401 : vous n'êtes pas authentifié, jeton absent ou invalide.";
  if (status === 403) return "Erreur 403 : action réservée au valideur.";
  if (status === 500) return "Erreur 500 : problème côté serveur, réessayez plus tard.";
  return "Erreur inattendue (code " + status + ").";
}

async function charger() {
  erreur.textContent = '';
  try {
    const r = await fetch(API, { headers: headers() });
    if (!r.ok) { liste.textContent = ''; erreur.textContent = messageErreur(r.status); return; }
    const data = await r.json();
    liste.textContent = '';
    data.forEach(d => {
      const li = document.createElement('li');
      li.textContent = '#' + d.id + ' - ' + d.titre + ' : ' + d.description + ' [' + d.statut + '] ';
      if (d.statut !== 'validée') {
        const b = document.createElement('button');
        b.textContent = 'Valider';
        b.addEventListener('click', () => valider(d.id));
        li.appendChild(b);
      }
      liste.appendChild(li);
    });
  } catch (err) {
    erreur.textContent = "Impossible de joindre l'API (réseau).";
  }
}

async function valider(id) {
  erreur.textContent = '';
  succes.textContent = '';
  const r = await fetch(API + '/' + id + '/valider', { method: 'POST', headers: headers() });
  if (!r.ok) { erreur.textContent = messageErreur(r.status); return; }
  succes.textContent = 'Demande #' + id + ' validée.';
  charger();
}

form.addEventListener('submit', async (e) => {
  e.preventDefault();
  erreur.textContent = '';
  succes.textContent = '';
  try {
    const r = await fetch(API, {
      method: 'POST',
      headers: headers(),
      body: JSON.stringify({
        titre: document.getElementById('titre').value,
        description: document.getElementById('description').value
      })
    });
    if (!r.ok) { erreur.textContent = messageErreur(r.status); return; }
    succes.textContent = 'Demande créée.';
    document.getElementById('titre').value = '';
    document.getElementById('description').value = '';
    charger();
  } catch (err) {
    erreur.textContent = "Impossible de joindre l'API (réseau).";
  }
});

document.getElementById('actualiser').addEventListener('click', charger);
charger();
