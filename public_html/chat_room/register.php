<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
		$errors[] = 'Invalid CSRF token';
	} else {
		$email = trim((string)($_POST['email'] ?? ''));
		$username = trim((string)($_POST['username'] ?? ''));
		$password = (string)($_POST['password'] ?? '');
		$confirm = (string)($_POST['confirm_password'] ?? '');

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$errors[] = 'Email tidak valid';
		}
		if ($username === '' || strlen($username) < 3) {
			$errors[] = 'Username minimal 3 karakter';
		}
		if (strlen($password) < 6) {
			$errors[] = 'Password minimal 6 karakter';
		}
		if ($password !== $confirm) {
			$errors[] = 'Konfirmasi password tidak sama';
		}

		if (!$errors) {
			try {
				$pdo = get_pdo();
				$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
				$stmt->execute([$email]);
				if ($stmt->fetch()) {
					$errors[] = 'Email sudah terdaftar';
				} else {
					$hash = password_hash($password, PASSWORD_DEFAULT);
					$insert = $pdo->prepare('INSERT INTO users (email, username, password_hash) VALUES (?, ?, ?)');
					$insert->execute([$email, $username, $hash]);
					redirect('login.php');
				}
			} catch (Throwable $e) {
				$errors[] = 'Terjadi kesalahan server';
			}
		}
	}
}

include __DIR__ . '/partials/header.php';
?>

<div class="card stack">
	<h2>Register</h2>
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
			<label>Username</label>
			<input type="text" name="username" required>
		</div>
		<div class="form-row">
			<label>Password</label>
			<input type="password" name="password" required>
		</div>
		<div class="form-row">
			<label>Konfirmasi Password</label>
			<input type="password" name="confirm_password" required>
		</div>
		<div class="actions">
			<button type="submit">Buat Akun</button>
			<a class="btn" href="login.php">Sudah punya akun?</a>
		</div>
	</form>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>

