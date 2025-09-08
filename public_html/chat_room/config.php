<?php
declare(strict_types=1);

// Basic configuration for Chat Room app
// NOTE: For production, restrict file permissions and move secrets outside web root if possible.

session_start();

// Application base URL (adjust if your domain/path changes)
$APP_BASE_URL = 'https://multinteraktif.online/chat_room';

// Database credentials (provided by user)
$DB_HOST = 'localhost';
$DB_NAME = 'n1567943_chat-room-realtime_db';
$DB_USER = 'n1567943_chat-room-realtime_user';
$DB_PASS = 'Gitar222@@@';

// Upload settings
$UPLOAD_BASE_DIR = __DIR__ . '/uploads';
$MAX_IMAGE_WIDTH = 1280;
$MAX_IMAGE_HEIGHT = 1280;
$JPEG_QUALITY = 80;
$ALLOWED_IMAGE_MIME = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

// Create PDO instance (MySQL)
function get_pdo(): PDO {
	static $pdo = null;
	global $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS;
	if ($pdo === null) {
		$dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4";
		$options = [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES => false,
		];
		$pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
	}
	return $pdo;
}

// CSRF token utilities
function ensure_csrf_token(): void {
	if (empty($_SESSION['csrf_token'])) {
		$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
	}
}

function verify_csrf_token(string $token): bool {
	return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Auth utilities
function require_login(): void {
	if (empty($_SESSION['user'])) {
		header('Location: login.php');
		exit;
	}
}

function current_user(): ?array {
	return $_SESSION['user'] ?? null;
}

function require_role(string $role): void {
	if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== $role) {
		header('HTTP/1.1 403 Forbidden');
		echo 'Forbidden';
		exit;
	}
}

function redirect(string $path): void {
	global $APP_BASE_URL;
	$location = rtrim($APP_BASE_URL, '/') . '/' . ltrim($path, '/');
	header('Location: ' . $location);
	exit;
}

// Helper: ensure a directory exists
function ensure_dir(string $dirPath): void {
	if (!is_dir($dirPath)) {
		mkdir($dirPath, 0755, true);
	}
}

// Security headers (basic)
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: no-referrer-when-downgrade');

?>

