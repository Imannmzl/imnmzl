<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

// Check if user is in session
if (empty($_SESSION['session_user'])) {
	redirect('join.php');
}

$session_user = $_SESSION['session_user'];
$pdo = get_pdo();

// Update last seen
try {
	$stmt = $pdo->prepare('UPDATE session_participants SET last_seen = NOW() WHERE session_id = ? AND username = ?');
	$stmt->execute([$session_user['session_id'], $session_user['username']]);
} catch (Throwable $e) {
	// Ignore error
}

// Get room info
$stmt = $pdo->prepare('SELECT * FROM rooms WHERE slug = ?');
$stmt->execute([$session_user['room_slug']]);
$room = $stmt->fetch();

if (!$room) {
	$_SESSION['error'] = 'Room tidak ditemukan';
	unset($_SESSION['session_user']);
	redirect('join.php');
}

// Get other participants
$stmt = $pdo->prepare('
	SELECT username, last_seen 
	FROM session_participants 
	WHERE session_id = ? AND username != ?
	ORDER BY last_seen DESC
');
$stmt->execute([$session_user['session_id'], $session_user['username']]);
$participants = $stmt->fetchAll();

include __DIR__ . '/partials/header.php';
?>

<div class="chat-layout">
	<aside class="card mobile-sidebar">
		<!-- Desktop content (hidden on mobile) -->
		<div class="desktop-content">
			<h3>Room: #<?= htmlspecialchars($room['name']) ?></h3>
			<div class="stack" style="margin-top:16px;">
				<h4>Peserta Online</h4>
				<div id="online-list" class="online-list muted">
					<div class="online-user">
						<span class="dot"></span>
						<span><?= htmlspecialchars($session_user['username']) ?> (Anda)</span>
					</div>
					<?php foreach ($participants as $p): ?>
						<div class="online-user">
							<span class="dot"></span>
							<span><?= htmlspecialchars($p['username']) ?></span>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		
		<!-- Mobile accordion (hidden on desktop) -->
		<div class="accordion-item">
			<button class="accordion-header" data-target="room-content">
				<span>Room Info</span>
				<svg class="accordion-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</button>
			<div class="accordion-content" id="room-content">
				<div class="stack">
					<h4>Room: #<?= htmlspecialchars($room['name']) ?></h4>
					<p class="muted">Anda bergabung sebagai: <strong><?= htmlspecialchars($session_user['username']) ?></strong></p>
				</div>
			</div>
		</div>
		
		<!-- Online accordion -->
		<div class="accordion-item">
			<button class="accordion-header" data-target="online-content">
				<span>Peserta Online</span>
				<svg class="accordion-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</button>
			<div class="accordion-content" id="online-content">
				<div id="online-list-mobile" class="online-list muted">
					<div class="online-user">
						<span class="dot"></span>
						<span><?= htmlspecialchars($session_user['username']) ?> (Anda)</span>
					</div>
					<?php foreach ($participants as $p): ?>
						<div class="online-user">
							<span class="dot"></span>
							<span><?= htmlspecialchars($p['username']) ?></span>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</aside>
	
	<section class="chat-section">
		<div class="card stack">
			<div class="actions">
				<strong>Room:</strong>
				<span>#<?= htmlspecialchars($room['name']) ?></span>
				<span class="muted">(<?= htmlspecialchars($session_user['username']) ?>)</span>
			</div>
			<div class="messages" id="messages"></div>
		</div>
		
		<!-- Sticky input area -->
		<div class="sticky-composer">
			<form id="composer" class="composer" enctype="multipart/form-data">
				<div class="input-wrapper">
					<textarea id="message-input" placeholder="Tulis pesan..." autocomplete="off" rows="3"></textarea>
					<label for="image-input" class="upload-icon" title="Upload gambar">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							<polyline points="7,10 12,15 17,10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							<line x1="12" y1="15" x2="12" y2="3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</label>
					<input type="file" id="image-input" accept="image/*" style="display: none;" />
				</div>
				<button type="submit">Kirim</button>
			</form>
			
			<!-- Image preview -->
			<div id="image-preview" class="image-preview" style="display: none;">
				<div class="preview-content">
					<img id="preview-img" src="" alt="Preview" />
					<div class="preview-info">
						<span id="preview-name"></span>
						<button type="button" id="remove-image" class="remove-btn" title="Hapus gambar">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</button>
					</div>
				</div>
			</div>
			
			<div id="upload-progress" class="progress" style="display:none;">
				<div class="track"><div class="bar"></div></div>
				<span class="pct">0%</span>
			</div>
		</div>
	</section>
</div>

<script>
window.SESSION_USER = <?= json_encode([
	'session_id' => $session_user['session_id'],
	'username' => $session_user['username'],
	'room_slug' => $session_user['room_slug']
]) ?>;
</script>
<script src="https://www.gstatic.com/firebasejs/10.12.4/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.4/firebase-database-compat.js"></script>
<script src="assets/session_chat.js?v=20250109"></script>

<?php include __DIR__ . '/partials/footer.php'; ?>