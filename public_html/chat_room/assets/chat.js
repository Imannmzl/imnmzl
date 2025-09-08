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
	el.innerHTML = `
		<div class="avatar">${initials}</div>
		<div>
			<div class="meta">${msg.username} • ${formatTime(msg.created_at || Date.now())}</div>
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

// initial subscribe
subscribeRoom(currentRoom);

