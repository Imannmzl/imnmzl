<?php
declare(strict_types=1);
require_once __DIR__ . '/../config.php';
require_login();
require_role('dosen');
$pdo = get_pdo();
$rooms = $pdo->query('SELECT slug, name FROM rooms ORDER BY name')->fetchAll();

// For client-side to call Firebase REST directly, we expose databaseURL via small inline script
// Alternatively you can proxy via PHP if you want to hide the DB URL.
include __DIR__ . '/../partials/header.php';
?>

<div class="stack">
	<h2>Moderasi Pesan</h2>
	<div class="card stack">
		<div class="form-row">
			<label>Pilih Room</label>
			<select id="mod-room">
				<?php foreach ($rooms as $r): ?>
					<option value="<?= htmlspecialchars($r['slug']) ?>">#<?= htmlspecialchars($r['name']) ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="actions">
			<button id="reload">Muat Ulang</button>
		</div>
	</div>

	<div class="card stack">
		<h3>Pesan Terakhir</h3>
		<div id="mod-messages" class="messages" style="height:50vh;"></div>
	</div>
</div>

<script src="https://www.gstatic.com/firebasejs/10.12.4/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.4/firebase-database-compat.js"></script>
<script src="../assets/chat.js"></script>
<script>
// Use same config as chat page by inlining from assets/chat.js? For simplicity, re-declare minimal init:
// IMPORTANT: If chat.js config changes, replicate here or refactor to load a shared file.
// Initialize using config exposed by chat.js
if (!firebase.apps.length) { firebase.initializeApp(window.firebaseConfig); }
const db = firebase.database();

const listEl = document.getElementById('mod-messages');
const roomSel = document.getElementById('mod-room');
const reloadBtn = document.getElementById('reload');

function renderItem(key, val, room) {
	const wrap = document.createElement('div');
	wrap.className = 'message';
	const initials = (val.username || '?').slice(0,2).toUpperCase();
	wrap.innerHTML = `
		<div class="avatar">${initials}</div>
		<div>
			<div class="meta">${val.username} • ${new Date(val.created_at||Date.now()).toLocaleString()}</div>
			<div class="bubble">${(val.text||'').replace(/</g,'&lt;')}${val.image_url ? `<img src="${val.image_url}" class="img-thumb" />` : ''}</div>
			<div class="actions" style="margin-top:6px;">
				<button class="secondary" onclick="deleteMessage('${room}','${key}')">Hapus</button>
			</div>
		</div>
	`;
	listEl.appendChild(wrap);
}

function loadRoom(room) {
	listEl.innerHTML = '';
	db.ref(`rooms/${room}/messages`).limitToLast(200).once('value', (snap) => {
		snap.forEach((child) => {
			renderItem(child.key, child.val(), room);
		});
	});
}

window.deleteMessage = function(room, key) {
	if (!confirm('Hapus pesan ini?')) return;
	db.ref(`rooms/${room}/messages/${key}`).remove();
	loadRoom(roomSel.value);
}

reloadBtn.addEventListener('click', () => loadRoom(roomSel.value));
roomSel.addEventListener('change', () => loadRoom(roomSel.value));
loadRoom(roomSel.value);
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>

