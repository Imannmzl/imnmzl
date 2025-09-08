<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';
require_login();
$user = current_user();
ensure_dir(__DIR__ . '/uploads');
// Fetch rooms dynamically
$pdo = get_pdo();
$rooms = $pdo->query('SELECT slug, name FROM rooms ORDER BY name')->fetchAll();
?>
<?php include __DIR__ . '/partials/header.php'; ?>

<div class="chat-layout">
	<aside class="card">
		<h3>Rooms</h3>
		<div class="room-list" id="room-list">
			<div class="muted">Default: general</div>
		</div>
	</aside>
	<section class="stack" style="min-width:0;">
		<div class="card stack">
			<div class="actions">
				<strong>Room:</strong>
				<select id="room-select">
					<?php foreach ($rooms as $r): ?>
						<option value="<?= htmlspecialchars($r['slug']) ?>">#<?= htmlspecialchars($r['name']) ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="messages" id="messages"></div>
			<form id="composer" class="composer" enctype="multipart/form-data">
				<input type="text" id="message-input" placeholder="Tulis pesan..." autocomplete="off" />
				<input type="file" id="image-input" accept="image/*" />
				<button type="submit">Kirim</button>
			</form>
		</div>
	</section>
</div>

<script>
window.APP_USER = <?= json_encode(['id' => $user['id'], 'username' => $user['username']]) ?>;
</script>
<script src="https://www.gstatic.com/firebasejs/10.12.4/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.4/firebase-database-compat.js"></script>
<script src="assets/chat.js"></script>

<?php include __DIR__ . '/partials/footer.php'; ?>

