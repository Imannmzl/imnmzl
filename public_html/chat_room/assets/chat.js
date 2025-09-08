// Firebase config - provided by user
const firebaseConfig = {
	apiKey: "AIzaSyC6Y44JXrCAuCPbjGbGxDAvZMVy4OkNSuc",
	authDomain: "chat-room-cde34.firebaseapp.com",
	databaseURL: "https://chat-room-cde34-default-rtdb.asia-southeast1.firebasedatabase.app",
	projectId: "chat-room-cde34",
	storageBucket: "chat-room-cde34.firebasestorage.app",
	messagingSenderId: "859236242502",
	appId: "1:859236242502:web:4e65bf833fce4e723ca647",
	measurementId: "G-M08F2EY2EB"
};

firebase.initializeApp(firebaseConfig);
const db = firebase.database();
// Expose config for moderation page reuse
window.firebaseConfig = firebaseConfig;

const roomSelect = document.getElementById('room-select');
const messagesDiv = document.getElementById('messages');
const composerForm = document.getElementById('composer');
const messageInput = document.getElementById('message-input');
const imageInput = document.getElementById('image-input');

let currentRoom = roomSelect.value;
let messagesRef = null;

function formatTime(ts) {
	const d = new Date(ts);
	return d.toLocaleString();
}

function renderMessage(msgId, msg) {
	const el = document.createElement('div');
	el.className = 'message';
	const initials = (msg.username || '?').slice(0, 2).toUpperCase();
	const imageHtml = msg.image_url ? `<div><img src="${msg.image_url}" class="img-thumb" alt="image" /></div>` : '';
	const canDelete = (window.APP_USER && window.APP_USER.role === 'dosen');
	const delBtn = canDelete ? `
		<button class="icon-btn" data-del="${msgId}" title="Hapus" aria-label="Hapus">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M3 6h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
				<path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
				<path d="M6 6l1 14a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-14" stroke="currentColor" stroke-width="2"/>
				<path d="M10 11v6M14 11v6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
			</svg>
		</button>
	` : '';
	el.innerHTML = `
		<div class="avatar">${initials}</div>
		<div>
			<div class="meta"><span>${msg.username} • ${formatTime(msg.created_at || Date.now())}</span>${delBtn}</div>
			<div class="bubble">${(msg.text || '').replace(/</g,'&lt;')}</div>
			${imageHtml}
		</div>
	`;
	messagesDiv.appendChild(el);
	messagesDiv.scrollTop = messagesDiv.scrollHeight;
}

function clearMessages() {
	messagesDiv.innerHTML = '';
}

function subscribeRoom(room) {
	if (messagesRef) messagesRef.off();
	clearMessages();
	messagesRef = db.ref(`rooms/${room}/messages`).limitToLast(100);
	messagesRef.on('child_added', snap => {
		renderMessage(snap.key, snap.val());
	});
}

roomSelect.addEventListener('change', () => {
	currentRoom = roomSelect.value;
	subscribeRoom(currentRoom);
});

composerForm.addEventListener('submit', async (e) => {
	e.preventDefault();
	const text = messageInput.value.trim();
	let imageUrl = '';
	if (imageInput.files && imageInput.files[0]) {
		const form = new FormData();
		form.append('image', imageInput.files[0]);
		try {
			const res = await fetch('upload_image.php', { method: 'POST', body: form });
			const data = await res.json();
			if (data.ok) { imageUrl = data.url; }
		} catch (err) {
			console.error(err);
		}
	}
	if (!text && !imageUrl) return;
	const payload = {
		user_id: window.APP_USER.id,
		username: window.APP_USER.username,
		text,
		image_url: imageUrl,
		created_at: Date.now()
	};
	db.ref(`rooms/${currentRoom}/messages`).push(payload);
	messageInput.value = '';
	imageInput.value = '';
});

// Delegate delete clicks
messagesDiv.addEventListener('click', async (e) => {
	const btn = e.target.closest('button[data-del]');
	if (!btn) return;
	const key = btn.getAttribute('data-del');
	if (!key) return;
	if (!confirm('Hapus pesan ini?')) return;
	// Try to load image_url first, then remove from Firebase and server if present
	const snap = await db.ref(`rooms/${currentRoom}/messages/${key}`).get();
	const val = snap.val() || {};
	if (val.image_url) {
		try {
			const form = new FormData();
			form.append('csrf_token', window.CSRF_TOKEN || '');
			form.append('url', val.image_url);
			await fetch('delete_uploaded_image.php', { method: 'POST', body: form });
		} catch (err) { console.error(err); }
	}
	db.ref(`rooms/${currentRoom}/messages/${key}`).remove();
	messagesDiv.innerHTML = '';
	subscribeRoom(currentRoom);
});

// Delete all Firebase data for a room (dosen only)
window.deleteRoomData = function(roomSlug) {
	if (!window.APP_USER || window.APP_USER.role !== 'dosen') return alert('Akses ditolak');
	if (!confirm(`Hapus semua data pesan untuk room #${roomSlug}?`)) return;
	db.ref(`rooms/${roomSlug}`).remove().then(() => {
		if (roomSelect.value === roomSlug) {
			messagesDiv.innerHTML = '';
		}
		alert('Data Firebase room dihapus');
	}).catch(err => {
		console.error(err);
		alert('Gagal menghapus data Firebase');
	});
}

// initial subscribe
subscribeRoom(currentRoom);

