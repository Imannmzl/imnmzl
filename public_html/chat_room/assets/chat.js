// Firebase config placeholders - replace with your project keys in production
const firebaseConfig = {
	apiKey: "YOUR_API_KEY",
	authDomain: "YOUR_PROJECT_ID.firebaseapp.com",
	databaseURL: "https://YOUR_PROJECT_ID-default-rtdb.asia-southeast1.firebasedatabase.app",
	projectId: "YOUR_PROJECT_ID",
	storageBucket: "YOUR_PROJECT_ID.appspot.com",
	messagingSenderId: "",
	appId: ""
};

firebase.initializeApp(firebaseConfig);
const db = firebase.database();

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

