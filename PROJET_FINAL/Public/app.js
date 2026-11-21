const $ = (s) => document.querySelector(s);
const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
let csrf = '', moi = null;
const estGestion = () => ['ADMIN', 'RESPONSABLE'].includes(moi.role);

async function api(chemin, methode = 'GET', corps) {
  const r = await fetch('/api/' + chemin, {
    method: methode,
    headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf },
    body: corps ? JSON.stringify(corps) : undefined,
  });
  const d = await r.json();
  if (!r.ok) throw new Error(d.erreur || 'Erreur');
  return d;
}
const erreur = (e) => { const m = $('#msg'); if (m) m.textContent = e.message; };

function vueConnexion() {
  $('#session').innerHTML = '';
  $('#app').innerHTML = `<div class="carte"><h2>Connexion</h2><p id="msg"></p>
    <form id="login"><label>Email<input name="email" type="email" required></label>
    <label>Mot de passe<input name="mot_de_passe" type="password" required></label><button>Se connecter</button></form></div>`;
  $('#login').onsubmit = async (e) => {
    e.preventDefault();
    try { const d = await api('login', 'POST', Object.fromEntries(new FormData(e.target))); csrf = d.csrf; moi = d.user; await vuePrincipale(); }
    catch (err) { erreur(err); }
  };
}

async function vuePrincipale() {
  $('#session').innerHTML = `${esc(moi.nom)} (${moi.role}) <button class="secondaire" id="logout">Déconnexion</button>`;
  $('#logout').onclick = async () => { await api('logout', 'POST'); moi = null; vueConnexion(); };

  const demandes = await api('demandes');
  let html = '<p id="msg"></p>';

  if (estGestion()) {
    const s = await api('stats');
    html += `<div class="carte stats">${s.par_statut.map((x) => `<div class="stat">${esc(x.statut)} : <b>${x.nombre}</b></div>`).join('')}<div class="stat">Sans encadreur : <b>${s.sans_encadreur}</b></div></div>`;
  }
  if (moi.role === 'ETUDIANT') {
    const ent = await api('entreprises');
    html += `<div class="carte"><h2>Nouvelle demande</h2><form id="nouvelle">
      <label>Entreprise<select name="entreprise_id">${ent.map((e) => `<option value="${e.id}">${esc(e.nom)} (${esc(e.ville)})</option>`).join('')}</select></label>
<button>Créer la demande</button></form></div>`;
  }
  const encs = estGestion() ? await api('encadreurs') : [];

  html += `<div class="tableau"><table><tr><th>#</th><th>Étudiant</th><th>Entreprise</th><th>Déposée le</th><th>Statut</th><th>Actions</th></tr>` +
    demandes.map((d) => {
      let a = '';
      if (moi.role === 'ETUDIANT' && d.statut === 'BROUILLON') a = `<button data-t="SOUMISE" data-id="${d.id}">Soumettre</button>`;
      if (moi.role === 'ETUDIANT' && d.statut === 'REFUSEE') a = `<button data-t="BROUILLON" data-id="${d.id}">Repasser en brouillon</button>`;
      if (estGestion() && d.statut === 'SOUMISE') a = `<button data-t="VALIDEE" data-id="${d.id}">Valider</button> <button class="secondaire" data-t="REFUSEE" data-id="${d.id}">Refuser</button>`;
      if (estGestion() && d.statut === 'VALIDEE' && !d.encadreur_id) a = `<select id="enc${d.id}">${encs.map((e) => `<option value="${e.id}">${esc(e.nom)}</option>`).join('')}</select> <button data-aff="${d.id}">Affecter</button>`;
      return `<tr><td>${d.id}</td><td>${esc(d.etudiant)}</td><td>${esc(d.entreprise)}</td><td>${esc(d.date_depot)}</td>
        <td><span class="badge ${d.statut}">${d.statut}</span>${d.motif_refus ? `<br><small>${esc(d.motif_refus)}</small>` : ''}</td><td>${a}</td></tr>`;
    }).join('') + '</table></div>';
  $('#app').innerHTML = html;

  const f = $('#nouvelle');
  if (f) f.onsubmit = async (e) => { e.preventDefault(); try { await api('demandes', 'POST', Object.fromEntries(new FormData(f))); await vuePrincipale(); } catch (err) { erreur(err); } };

  $('#app').onclick = async (e) => {
    try {
      const t = e.target.dataset;
      if (t.t) {
        const corps = { statut: t.t };
        if (t.t === 'REFUSEE') { corps.motif = prompt('Motif du refus (obligatoire) :') || ''; }
        await api(`demandes/${t.id}/transition`, 'POST', corps);
      } else if (t.aff) {
        await api(`demandes/${t.aff}/encadreur`, 'POST', { encadreur_id: Number($(`#enc${t.aff}`).value) });
      } else return;
      await vuePrincipale();
    } catch (err) { erreur(err); }
  };
}

(async () => {
  try { const d = await api('moi'); csrf = d.csrf; moi = d.user; await vuePrincipale(); } catch { vueConnexion(); }
})();
