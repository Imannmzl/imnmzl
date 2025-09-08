<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
		$errors[] = 'Invalid CSRF token';
	} else {
		$email = trim((string)($_POST['email'] ?? ''));
		$password = (string)($_POST['password'] ?? '');

		try {
			$pdo = get_pdo();
			$stmt = $pdo->prepare('SELECT id, email, username, password_hash, role FROM users WHERE email = ?');
			$stmt->execute([$email]);
			$user = $stmt->fetch();
			if ($user && password_verify($password, $user['password_hash'])) {
				$_SESSION['user'] = [
					'id' => $user['id'],
					'email' => $user['email'],
					'username' => $user['username'],
					'role' => $user['role'],
				];
				if ($user['role'] === 'dosen') {
					redirect('dosen/index.php');
				} else {
					redirect('chat.php');
				}
			} else {
				$errors[] = 'Email atau password salah';
			}
		} catch (Throwable $e) {
			$errors[] = 'Terjadi kesalahan server';
		}
	}
}

include __DIR__ . '/partials/header.php';
?>

<div class="card stack">
	<h2>Login</h2>
	<?php if ($errors): ?>
		<div class="muted">
			<?php foreach ($errors as $err): ?>
				<div>- <?= htmlspecialchars($err) ?></div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
	<form method="post" class="stack" autocomplete="off" novalidate>
		<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
		<div class="form-row">
			<label>Email</label>
			<input type="email" name="email" required>
		</div>
		<div class="form-row">
			<label>Password</label>
			<input type="password" name="password" required>
		</div>
		<div class="actions">
			<button type="submit">Masuk</button>
			<a class="btn" href="register.php">Daftar</a>
		</div>
	</form>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>

