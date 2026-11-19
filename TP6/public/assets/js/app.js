
const API = '/tp6_api_demandes/public';

let currentUser = null;
let requests = [];
let rejectId = null;

const $ = id => document.getElementById(id);

const isResponsible = () =>
    currentUser && currentUser.role === 'RESPONSABLE';

/* COMMUNICATION AVEC L'API */

async function api(path, options = {}) {
    let response;

    try {
        response = await fetch(API + path, {
            credentials: 'same-origin',
            ...options,
            headers: {
                'Content-Type': 'application/json',
                ...(options.headers || {})
            }
        });
    } catch (error) {
        throw new Error(
            "Impossible de joindre l'API. Vérifiez Apache, MySQL et l'adresse de l'interface."
        );
    }

    let body = {};

    try {
        body = await response.json();
    } catch (_) {}

    if (!response.ok) {
        throw new Error(
            body.erreur || body.message || `Erreur HTTP ${response.status}`
        );
    }

    return body;
}

/* OUTILS D'AFFICHAGE */

function toast(message) {
    $('toast').textContent = message;
    $('toast').classList.remove('hidden');

    setTimeout(() => {
        $('toast').classList.add('hidden');
    }, 3500);
}

function safe(value) {
    return String(value ?? '').replace(/[&<>"']/g, c => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;'
    }[c]));
}

function getRows(body) {
    if (Array.isArray(body)) return body;

    for (const key of ['demandes', 'data', 'results']) {
        if (Array.isArray(body?.[key])) return body[key];
    }

    return [];
}

function statusBadge(status) {
    const value = String(status || 'INCONNU').toUpperCase();

    return `<span class="badge b-${safe(value)}">${safe(value)}</span>`;
}

function dateFormat(value) {
    if (!value) return '—';

    const date = new Date(
        value + (String(value).length === 10 ? 'T00:00:00' : '')
    );

    return isNaN(date)
        ? safe(value)
        : date.toLocaleDateString('fr-FR');
}

/* TABLEAU DES DEMANDES */

function tableMarkup(rows, responsible = false) {
    if (!rows.length) {
        return `
            <div class="empty">
                <strong>Aucune demande pour le moment</strong>
                <p>Les demandes apparaîtront ici dès leur création.</p>
            </div>
        `;
    }

    const lines = rows.map(r => {
        const id = Number(r.id);
        const status = String(r.statut || '').toUpperCase();

        let buttons = `
            <button class="btn light"
                data-action="details"
                data-id="${id}">
                Détails
            </button>
        `;

        if (!responsible && status === 'BROUILLON') {
            buttons += `
                <button class="btn primary"
                    data-action="submit"
                    data-id="${id}">
                    Soumettre
                </button>
            `;
        }

        if (!responsible && status === 'REFUSEE') {
            buttons += `
                <button class="btn light"
                    data-action="reopen"
                    data-id="${id}">
                    Reprendre
                </button>
            `;
        }

        if (responsible && status === 'SOUMISE') {
            buttons += `
                <button class="btn success"
                    data-action="approve"
                    data-id="${id}">
                    Valider
                </button>

                <button class="btn danger"
                    data-action="reject"
                    data-id="${id}">
                    Refuser
                </button>
            `;
        }

        return `
            <tr>
                <td>
                    <strong>${safe(r.entreprise)}</strong>
                    <br>
                    <small>Réf. #${id}</small>
                </td>

                ${responsible
                    ? `<td>${safe(r.nom_etudiant || r.email_etudiant || '—')}</td>`
                    : ''
                }

                <td>${dateFormat(r.date_debut)}</td>
                <td>${statusBadge(status)}</td>
                <td><div class="actions">${buttons}</div></td>
            </tr>
        `;
    }).join('');

    return `
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Entreprise</th>
                        ${responsible ? '<th>Étudiant</th>' : ''}
                        <th>Date de début</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>${lines}</tbody>
            </table>
        </div>
    `;
}

/* CHARGEMENT DES DEMANDES */

async function loadRequests() {
    const path = isResponsible()
        ? '/api/demandes'
        : '/api/mes-demandes';

    const body = await api(path);

    requests = getRows(body);

    renderDashboard();

    $('allTable').innerHTML = tableMarkup(
        requests,
        isResponsible()
    );

    fillHistorySelect();
}

/* TABLEAU DE BORD */

function renderDashboard() {
    const count = status =>
        requests.filter(r => r.statut === status).length;

    $('statTotal').textContent = requests.length;
    $('statDraft').textContent = count('BROUILLON');
    $('statPending').textContent = count('SOUMISE');
    $('statApproved').textContent = count('VALIDEE');

    $('recentTable').innerHTML = tableMarkup(
        requests.slice(0, 5),
        isResponsible()
    );

    const firstName = (currentUser.nom || 'utilisateur').split(' ')[0];

    $('welcomeTitle').textContent = `Bonjour, ${firstName}`;

    $('welcomeSub').textContent = isResponsible()
        ? 'Voici les demandes de stage à examiner.'
        : 'Voici un aperçu de vos demandes de stage.';

    $('recentTitle').textContent = isResponsible()
        ? 'Demandes récentes'
        : 'Mes demandes récentes';

    $('quickNew').classList.toggle('hidden', isResponsible());
    $('listNew').classList.toggle('hidden', isResponsible());
    $('newNav').classList.toggle('hidden', isResponsible());

    $('requestsNav').textContent = isResponsible()
        ? 'Toutes les demandes'
        : 'Mes demandes';

    $('listTitle').textContent = isResponsible()
        ? 'Toutes les demandes'
        : 'Mes demandes';
}

/* NAVIGATION */

function showPage(page) {
    if (page === 'new' && isResponsible()) {
        toast('Cette action est réservée aux étudiants.');
        return;
    }

    const pages = [
        'dashboard',
        'requests',
        'new',
        'history'
    ];

    pages.forEach(p => {
        $(`${p}Page`).classList.toggle('hidden', p !== page);
    });

    document.querySelectorAll('.nav-btn').forEach(button => {
        button.classList.toggle(
            'active',
            button.dataset.page === page
        );
    });

    const labels = {
        dashboard: 'Tableau de bord',
        requests: isResponsible() ? 'Toutes les demandes' : 'Mes demandes',
        new: 'Nouvelle demande',
        history: 'Historique'
    };

    $('pageName').textContent = labels[page] || 'Accueil';
}

function enterApp(user) {
    currentUser = user;

    $('loginView').classList.add('hidden');
    $('appView').classList.remove('hidden');

    $('userName').textContent = user.nom || user.email;
    $('avatar').textContent =
        (user.nom || user.email || 'U').trim().charAt(0).toUpperCase();

    showPage('dashboard');

    loadRequests().catch(error => toast(error.message));
}

/* CONNEXION */

$('loginForm').addEventListener('submit', async event => {
    event.preventDefault();

    $('loginError').textContent = '';
    $('loginError').classList.add('hidden');

    $('loginBtn').disabled = true;
    $('loginBtn').textContent = 'Connexion...';

    try {
        const body = await api('/api/connexion', {
            method: 'POST',
            body: JSON.stringify({
                email: $('email').value.trim(),
                mot_de_passe: $('password').value
            })
        });

        if (!body.utilisateur) {
            throw new Error('Utilisateur absent de la réponse.');
        }

        enterApp(body.utilisateur);

    } catch (error) {
        $('loginError').textContent = error.message;
        $('loginError').classList.remove('hidden');

    } finally {
        $('loginBtn').disabled = false;
        $('loginBtn').textContent = 'Se connecter';
    }
});

/* DECONNEXION */

$('logoutBtn').addEventListener('click', async () => {
    try {
        await api('/api/deconnexion', {
            method: 'POST',
            body: '{}'
        });
    } catch (error) {
        toast(error.message);
    }

    currentUser = null;
    requests = [];

    $('appView').classList.add('hidden');
    $('loginView').classList.remove('hidden');
    $('password').value = '';
});

/* BOUTONS DE NAVIGATION */

document.querySelectorAll('.nav-btn').forEach(button => {
    button.addEventListener('click', () => {
        if (button.dataset.page) {
            showPage(button.dataset.page);
        }
    });
});

document.querySelectorAll('[data-goto]').forEach(button => {
    button.addEventListener('click', () => {
        showPage(button.dataset.goto);
    });
});

$('quickNew').addEventListener('click', () => showPage('new'));
$('listNew').addEventListener('click', () => showPage('new'));

/* ACTUALISATION */

$('refreshBtn').addEventListener('click', () => {
    loadRequests()
        .then(() => toast('Liste actualisée.'))
        .catch(error => toast(error.message));
});

/* CREATION D'UNE DEMANDE */

$('createForm').addEventListener('submit', async event => {
    event.preventDefault();

    try {
        const body = await api('/api/demandes', {
            method: 'POST',
            body: JSON.stringify({
                entreprise: $('company').value.trim(),
                date_debut: $('startDate').value
            })
        });

        $('createForm').reset();

        await loadRequests();

        showPage('requests');

        toast(body.message || 'Demande créée.');

    } catch (error) {
        toast(error.message);
    }
});

/* CHANGEMENT DE STATUT */

async function changeStatus(id, statut, motif = null) {
    await api(`/api/demandes/${id}/statut`, {
        method: 'PATCH',
        body: JSON.stringify({ statut, motif })
    });

    await loadRequests();

    toast('Statut mis à jour.');
}

/* ACTIONS SUR LES DEMANDES */

document.addEventListener('click', async event => {
    const button = event.target.closest('[data-action]');

    if (!button) return;

    const id = Number(button.dataset.id);
    const action = button.dataset.action;

    try {
        if (action === 'details') {
            const body = await api(`/api/demandes/${id}`);
            const d = body.demande;

            alert(
                `Demande #${d.id}\n` +
                `Entreprise : ${d.entreprise}\n` +
                `Date : ${d.date_debut}\n` +
                `Statut : ${d.statut}`
            );
        }

        if (action === 'submit') {
            await changeStatus(id, 'SOUMISE');
        }

        if (action === 'reopen') {
            await changeStatus(id, 'BROUILLON');
        }

        if (
            action === 'approve' &&
            confirm('Confirmer la validation de cette demande ?')
        ) {
            await changeStatus(id, 'VALIDEE');
        }

        if (action === 'reject') {
            rejectId = id;
            $('rejectReason').value = '';
            $('rejectModal').classList.remove('hidden');
        }

    } catch (error) {
        toast(error.message);
    }
});

/* REFUS D'UNE DEMANDE */

$('cancelReject').addEventListener('click', () => {
    $('rejectModal').classList.add('hidden');
    rejectId = null;
});

$('rejectForm').addEventListener('submit', async event => {
    event.preventDefault();

    if (!rejectId) return;

    try {
        await changeStatus(
            rejectId,
            'REFUSEE',
            $('rejectReason').value.trim()
        );

        $('rejectModal').classList.add('hidden');
        rejectId = null;

    } catch (error) {
        toast(error.message);
    }
});

/* HISTORIQUE */

function fillHistorySelect() {
    const select = $('historyDemand');

    select.innerHTML = '<option value="">Choisir une demande</option>';

    requests.forEach(d => {
        const option = document.createElement('option');

        option.value = d.id;
        option.textContent = `#${d.id} - ${d.entreprise}`;

        select.appendChild(option);
    });
}

$('historyDemand').addEventListener('change', async () => {
    const id = $('historyDemand').value;

    if (!id) {
        $('historyContent').textContent = 'Sélectionnez une demande.';
        return;
    }

    try {
        const body = await api(`/api/demandes/${id}/historique`);
        const history = body.historique || [];

        if (!history.length) {
            $('historyContent').textContent = 'Aucun historique disponible.';
            return;
        }

        $('historyContent').innerHTML = history.map(item => `
            <div class="history-item">
                <strong>${safe(item.ancien_statut)}</strong>
                →
                <strong>${safe(item.nouveau_statut)}</strong>
                <p>Utilisateur : ${safe(item.nom_utilisateur)}</p>
                <p>Motif : ${safe(item.motif || 'Aucun')}</p>
                <small>${safe(item.created_at)}</small>
            </div>
        `).join('');

    } catch (error) {
        toast(error.message);
    }
});
