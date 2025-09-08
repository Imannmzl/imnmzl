<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

$session_id = trim((string)($_GET['session'] ?? ''));
$errors = [];
$session_info = null;

// Validate session
if ($session_id) {
	try {
		$pdo = get_pdo();
		$stmt = $pdo->prepare('
			SELECT s.*, r.name as room_name 
			FROM sessions s
			LEFT JOIN rooms r ON s.room_slug = r.slug
			WHERE s.id = ? AND s.is_active = 1
		');
		$stmt->execute([$session_id]);
		$session_info = $stmt->fetch();
		
		if (!$session_info) {
			$errors[] = 'Invite link tidak valid atau sudah tidak aktif';
		} elseif ($session_info['expires_at'] && strtotime($session_info['expires_at']) < time()) {
			$errors[] = 'Invite link sudah kadaluarsa';
		} else {
			// Check participant count
			$stmt = $pdo->prepare('SELECT COUNT(*) FROM session_participants WHERE session_id = ?');
			$stmt->execute([$session_id]);
			$participant_count = $stmt->fetchColumn();
			
			if ($participant_count >= $session_info['max_users']) {
				$errors[] = 'Room sudah penuh';
			}
		}
	} catch (Throwable $e) {
		$errors[] = 'Terjadi kesalahan server';
	}
} else {
	$errors[] = 'Session ID tidak ditemukan';
}

// Handle join
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $session_info && !$errors) {
	$username = trim((string)($_POST['username'] ?? ''));
	
	if (strlen($username) < 2) {
		$errors[] = 'Nama minimal 2 karakter';
	} elseif (strlen($username) > 50) {
		$errors[] = 'Nama maksimal 50 karakter';
	} else {
		try {
			// Check if username already exists in this session
			$stmt = $pdo->prepare('SELECT id FROM session_participants WHERE session_id = ? AND username = ?');
			$stmt->execute([$session_id, $username]);
			$existing_participant = $stmt->fetch();
			
			$is_rejoin = false;
			if (!$existing_participant) {
				// Add new participant
				$stmt = $pdo->prepare('INSERT INTO session_participants (session_id, username) VALUES (?, ?)');
				$stmt->execute([$session_id, $username]);
			} else {
				// Participant already exists - this is a rejoin
				$is_rejoin = true;
			}
			
			// Set session (allow rejoin)
			$_SESSION['session_user'] = [
				'session_id' => $session_id,
				'username' => $username,
				'room_slug' => $session_info['room_slug'],
				'room_name' => $session_info['room_name'],
				'is_rejoin' => $is_rejoin
			];
			
			redirect('session_chat.php');
		} catch (Throwable $e) {
			$errors[] = 'Gagal bergabung: ' . $e->getMessage();
		}
	}
}

include __DIR__ . '/partials/header.php';
?>

<div class="card stack" style="max-width: 400px; margin: 50px auto;">
	<h2>Bergabung ke Chat Room</h2>
	
	<?php if ($errors): ?>
		<div class="muted">
			<?php foreach ($errors as $err): ?>
				<div>- <?= htmlspecialchars($err) ?></div>
			<?php endforeach; ?>
		</div>
	<?php elseif ($session_info): ?>
		<div style="background: #10b981; color: white; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
			<strong>Room: #<?= htmlspecialchars($session_info['room_name']) ?></strong><br>
			<small>Anda akan bergabung sebagai mahasiswa</small>
		</div>
		
		<form method="post" class="stack">
			<div class="form-row">
				<label>Nama Anda</label>
				<input type="text" name="username" placeholder="Masukkan nama" required maxlength="50" autocomplete="off">
			</div>
			<div class="actions">
				<button type="submit">Bergabung</button>
			</div>
		</form>
	<?php endif; ?>
	
	<div style="text-align: center; margin-top: 20px;">
		<a href="login.php" class="btn">Login sebagai Dosen</a>
	</div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>