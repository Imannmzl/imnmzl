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
	<link rel="stylesheet" href="assets/styles.css?v=20250112">
</head>
<body>
<header class="site-header">
	<div class="container">
		<a class="brand" href="index.php">Chat Room</a>
		<nav class="nav">
			<?php if (!empty($_SESSION['user'])): $u = $_SESSION['user']; ?>
				<span class="welcome"><?= htmlspecialchars($u['username']) ?> (<?= htmlspecialchars($u['role'] ?? 'mahasiswa') ?>)</span>
				<button class="btn theme-toggle" id="theme-toggle" title="Ganti tema">
					<svg class="theme-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</button>
				<?php if (($u['role'] ?? '') === 'dosen'): ?>
					<?php if (basename($_SERVER['PHP_SELF']) !== 'chat.php'): ?>
						<a class="btn" href="chat.php">Chat Room</a>
					<?php endif; ?>
					<a class="btn" href="dosen/index.php">Dashboard</a>
				<?php endif; ?>
				<a class="btn" href="logout.php">Logout</a>
			<?php elseif (!empty($_SESSION['session_user'])): $su = $_SESSION['session_user']; ?>
				<span class="welcome"><?= htmlspecialchars($su['username']) ?> (Mahasiswa)</span>
				<button class="btn theme-toggle" id="theme-toggle" title="Ganti tema">
					<svg class="theme-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</button>
				<a class="btn" href="session_logout.php">Keluar</a>
			<?php else: ?>
				<button class="btn theme-toggle" id="theme-toggle" title="Ganti tema">
					<svg class="theme-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</button>
				<a class="btn" href="login.php">Login</a>
				<a class="btn" href="register.php">Register</a>
			<?php endif; ?>
		</nav>
	</div>
</header>
<main class="container">
<script>
window.CSRF_TOKEN = <?= json_encode($_SESSION['csrf_token']) ?>;

// Theme switching functionality
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;
    
    // Get saved theme or default to light
    const savedTheme = localStorage.getItem('theme') || 'light';
    body.setAttribute('data-theme', savedTheme);
    
    // Theme toggle click handler
    themeToggle.addEventListener('click', function() {
        const currentTheme = body.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        body.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
    });
});
</script>

