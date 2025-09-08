<?php
declare(strict_types=1);
require_once __DIR__ . '/../config.php';
ensure_csrf_token();
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Chat Room Realtime</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="assets/styles.css?v=20250908">
</head>
<body>
<header class="site-header">
	<div class="container">
		<a class="brand" href="index.php">Chat Room</a>
		<nav class="nav">
			<?php if (!empty($_SESSION['user'])): $u = $_SESSION['user']; ?>
				<span class="welcome">Hi, <?= htmlspecialchars($u['username']) ?> (<?= htmlspecialchars($u['role'] ?? 'mahasiswa') ?>)</span>
				<?php if (($u['role'] ?? '') === 'dosen'): ?>
					<a class="btn" href="dosen/index.php">Dashboard Dosen</a>
				<?php endif; ?>
				<a class="btn" href="logout.php">Logout</a>
			<?php else: ?>
				<a class="btn" href="login.php">Login</a>
				<a class="btn" href="register.php">Register</a>
			<?php endif; ?>
		</nav>
	</div>
</header>
<main class="container">
<script>
window.CSRF_TOKEN = <?= json_encode($_SESSION['csrf_token']) ?>;
</script>

