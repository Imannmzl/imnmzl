<?php
declare(strict_types=1);
require_once __DIR__ . '/../config.php';
require_login();
require_role('dosen');
$pdo = get_pdo();

$errors = [];
$success = '';

// Handle create invite link
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
		$errors[] = 'Invalid CSRF token';
	} else {
		$room_slug = trim((string)($_POST['room_slug'] ?? ''));
		$expires_hours = (int)($_POST['expires_hours'] ?? 24);
		$max_users = (int)($_POST['max_users'] ?? 50);
		
		if ($room_slug === '') {
			$errors[] = 'Room slug wajib diisi';
		} else {
			// Check if room exists
			$stmt = $pdo->prepare('SELECT id FROM rooms WHERE slug = ?');
			$stmt->execute([$room_slug]);
			if (!$stmt->fetch()) {
				$errors[] = 'Room tidak ditemukan';
			} else {
				try {
					// Generate session ID
					$session_id = bin2hex(random_bytes(16));
					$expires_at = $expires_hours > 0 ? date('Y-m-d H:i:s', time() + ($expires_hours * 3600)) : null;
					
					$stmt = $pdo->prepare('INSERT INTO sessions (id, room_slug, created_by, expires_at, max_users) VALUES (?, ?, ?, ?, ?)');
					$stmt->execute([$session_id, $room_slug, $_SESSION['user']['id'], $expires_at, $max_users]);
					
					$invite_url = rtrim($APP_BASE_URL, '/') . '/join.php?session=' . $session_id;
					$success = 'Invite link berhasil dibuat!';
				} catch (Throwable $e) {
					$errors[] = 'Gagal membuat invite link: ' . $e->getMessage();
				}
			}
		}
	}
}

// Get rooms for dropdown
$rooms = $pdo->query('SELECT slug, name FROM rooms ORDER BY name')->fetchAll();

// Get active sessions
$sessions = $pdo->query('
	SELECT s.*, r.name as room_name, 
	       COUNT(sp.id) as participant_count,
	       u.username as created_by_name
	FROM sessions s
	LEFT JOIN rooms r ON s.room_slug = r.slug
	LEFT JOIN session_participants sp ON s.id = sp.session_id
	LEFT JOIN users u ON s.created_by = u.id
	WHERE s.is_active = 1
	GROUP BY s.id
	ORDER BY s.created_at DESC
')->fetchAll();

include __DIR__ . '/../partials/header.php';
?>

<div class="stack">
	<h2>Buat Invite Link</h2>
	
	<?php if ($errors): ?>
		<div class="muted">
			<?php foreach ($errors as $err): ?>
				<div>- <?= htmlspecialchars($err) ?></div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
	
	<?php if ($success): ?>
		<div class="card" style="background: #10b981; color: white; border-color: #059669;">
			<strong><?= htmlspecialchars($success) ?></strong>
			<div style="margin-top: 8px;">
				<strong>Invite Link:</strong><br>
				<input type="text" value="<?= htmlspecialchars($invite_url ?? '') ?>" readonly style="width: 100%; margin-top: 4px; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: white;">
				<button onclick="navigator.clipboard.writeText('<?= htmlspecialchars($invite_url ?? '') ?>'); alert('Link disalin!');" style="margin-top: 8px;">Salin Link</button>
			</div>
		</div>
	<?php endif; ?>

	<div class="card stack">
		<h3>Buat Link Baru</h3>
		<form method="post" class="stack">
			<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
			
			<div class="form-row">
				<label>Room</label>
				<select name="room_slug" required>
					<option value="">Pilih Room</option>
					<?php foreach ($rooms as $room): ?>
						<option value="<?= htmlspecialchars($room['slug']) ?>">#<?= htmlspecialchars($room['name']) ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			
			<div class="form-row">
				<label>Kadaluarsa (jam)</label>
				<select name="expires_hours">
					<option value="1">1 jam</option>
					<option value="6">6 jam</option>
					<option value="24" selected>24 jam</option>
					<option value="72">3 hari</option>
					<option value="168">1 minggu</option>
					<option value="0">Tidak kadaluarsa</option>
				</select>
			</div>
			
			<div class="form-row">
				<label>Max Peserta</label>
				<input type="number" name="max_users" value="50" min="1" max="200">
			</div>
			
			<div class="actions">
				<button type="submit">Buat Invite Link</button>
			</div>
		</form>
	</div>

	<div class="card stack">
		<h3>Link Aktif</h3>
		<?php if (!$sessions): ?>
			<div class="muted">Belum ada invite link</div>
		<?php else: ?>
			<div class="stack">
				<?php foreach ($sessions as $session): ?>
					<div class="card" style="padding: 12px;">
						<div class="actions" style="justify-content: space-between; align-items: flex-start;">
							<div>
								<strong>Room: #<?= htmlspecialchars($session['room_name']) ?></strong><br>
								<small class="muted">
									Peserta: <?= (int)$session['participant_count'] ?>/<?= (int)$session['max_users'] ?> | 
									Dibuat: <?= date('d/m/Y H:i', strtotime($session['created_at'])) ?> |
									<?php if ($session['expires_at']): ?>
										Kadaluarsa: <?= date('d/m/Y H:i', strtotime($session['expires_at'])) ?>
									<?php else: ?>
										Tidak kadaluarsa
									<?php endif; ?>
								</small>
							</div>
							<div class="actions" style="gap: 8px;">
								<button onclick="copyLink('<?= htmlspecialchars($session['id']) ?>')" class="secondary">Salin Link</button>
								<button onclick="deactivateSession('<?= htmlspecialchars($session['id']) ?>')" class="secondary">Nonaktifkan</button>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</div>

<script>
function copyLink(sessionId) {
	const link = '<?= rtrim($APP_BASE_URL, '/') ?>/join.php?session=' + sessionId;
	navigator.clipboard.writeText(link).then(() => {
		alert('Link disalin!');
	});
}

function deactivateSession(sessionId) {
	if (!confirm('Nonaktifkan invite link ini?')) return;
	
	const form = document.createElement('form');
	form.method = 'POST';
	form.innerHTML = `
		<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
		<input type="hidden" name="action" value="deactivate">
		<input type="hidden" name="session_id" value="${sessionId}">
	`;
	document.body.appendChild(form);
	form.submit();
}
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>