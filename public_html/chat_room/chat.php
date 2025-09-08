<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';
require_login();
$user = current_user();
ensure_dir(__DIR__ . '/uploads');
// Handle Room CRUD (for dosen)
$pdo = get_pdo();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($user['role'] ?? '') === 'dosen')) {
	if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
		$errors[] = 'Invalid CSRF token';
	} else {
		$action = (string)($_POST['action'] ?? '');
		try {
			if ($action === 'create') {
				$slug = preg_replace('~[^a-z0-9-]~', '-', strtolower(trim((string)($_POST['slug'] ?? ''))));
				$name = trim((string)($_POST['name'] ?? ''));
				if ($slug === '' || $name === '') throw new RuntimeException('Slug dan nama wajib diisi');
				$stmt = $pdo->prepare('INSERT INTO rooms (slug, name, created_by) VALUES (?, ?, ?)');
				$stmt->execute([$slug, $name, $user['id']]);
			} elseif ($action === 'update') {
				$id = (int)($_POST['id'] ?? 0);
				$name = trim((string)($_POST['name'] ?? ''));
				if ($id <= 0 || $name === '') throw new RuntimeException('ID/Nama tidak valid');
				$stmt = $pdo->prepare('UPDATE rooms SET name = ? WHERE id = ?');
				$stmt->execute([$name, $id]);
			} elseif ($action === 'delete') {
				$id = (int)($_POST['id'] ?? 0);
				if ($id <= 0) throw new RuntimeException('ID tidak valid');
				$pdo->prepare('DELETE FROM rooms WHERE id = ?')->execute([$id]);
			}
		} catch (Throwable $e) {
			$errors[] = 'Gagal menyimpan: ' . $e->getMessage();
		}
	}
}
// Fetch rooms dynamically (after any change)
$rooms = $pdo->query('SELECT id, slug, name FROM rooms ORDER BY name')->fetchAll();
$dosenUsernames = $pdo->query("SELECT username FROM users WHERE role = 'dosen'")->fetchAll(PDO::FETCH_COLUMN);
?>
<?php include __DIR__ . '/partials/header.php'; ?>

<div class="chat-layout">
	<aside class="card mobile-sidebar">
		<!-- Rooms accordion -->
		<div class="accordion-item">
			<button class="accordion-header" data-target="rooms-content">
				<span>Rooms</span>
				<svg class="accordion-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</button>
			<div class="accordion-content" id="rooms-content">
				<div class="room-list" id="room-list">
					<?php if (($user['role'] ?? '') === 'dosen'): ?>
						<div class="stack" style="margin-bottom:12px;">
							<h4>Kelola Room</h4>
							<?php if (!empty($errors)): ?>
								<div class="muted">
									<?php foreach ($errors as $er): ?>
										<div>- <?= htmlspecialchars($er) ?></div>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
							<form method="post" class="stack">
								<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
								<input type="hidden" name="action" value="create">
								<div class="form-row">
									<label>Slug</label>
									<input type="text" name="slug" placeholder="mis: kelas-if101">
								</div>
								<div class="form-row">
									<label>Nama</label>
									<input type="text" name="name" placeholder="Kelas IF101">
								</div>
								<div class="actions">
									<button type="submit">Tambah Room</button>
								</div>
							</form>
						</div>
					<?php endif; ?>
					<div class="stack">
						<?php foreach ($rooms as $r): ?>
							<div class="actions" style="justify-content:space-between;">
								<div>#<?= htmlspecialchars($r['name']) ?> <span class="muted">(<?= htmlspecialchars($r['slug']) ?>)</span></div>
								<?php if (($user['role'] ?? '') === 'dosen'): ?>
									<form method="post" class="actions" style="gap:6px;">
										<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
										<input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
										<input type="text" name="name" value="<?= htmlspecialchars($r['name']) ?>" style="width:160px;">
										<button name="action" value="update" type="submit" class="secondary">Ubah</button>
										<button name="action" value="delete" type="submit" class="secondary" onclick="return confirm('Hapus room? Tidak menghapus data di Firebase');">Hapus</button>
									</form>
									<button type="button" class="secondary" onclick="deleteRoomData('<?= htmlspecialchars($r['slug']) ?>')">Hapus Data Firebase</button>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Online accordion -->
		<div class="accordion-item">
			<button class="accordion-header" data-target="online-content">
				<span>Mahasiswa Online</span>
				<svg class="accordion-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</button>
			<div class="accordion-content" id="online-content">
				<div id="online-list" class="online-list muted">Memuat...</div>
			</div>
		</div>
	</aside>
	<section class="chat-section">
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
			<div id="upload-progress" class="progress" style="display:none;">
				<div class="track"><div class="bar"></div></div>
				<span class="pct">0%</span>
			</div>
		</div>
	</section>
</div>

<script>
window.APP_USER = <?= json_encode(['id' => $user['id'], 'username' => $user['username'], 'role' => $user['role'] ?? 'mahasiswa']) ?>;
window.DOSEN_USERS = <?= json_encode(array_values($dosenUsernames)) ?>;
</script>
<script src="https://www.gstatic.com/firebasejs/10.12.4/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.4/firebase-database-compat.js"></script>
<script src="assets/chat.js?v=20250908"></script>

<?php include __DIR__ . '/partials/footer.php'; ?>

