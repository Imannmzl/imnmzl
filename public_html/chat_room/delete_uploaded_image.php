<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';
require_login();
require_role('dosen');

header('Content-Type: application/json');

try {
	if (!verify_csrf_token((string)($_POST['csrf_token'] ?? ''))) {
		throw new RuntimeException('Invalid CSRF token');
	}
	$url = (string)($_POST['url'] ?? '');
	if ($url === '') throw new RuntimeException('URL kosong');

	global $APP_BASE_URL, $UPLOAD_BASE_DIR;
	$baseUploadsUrl = rtrim($APP_BASE_URL, '/') . '/uploads/';
	if (strpos($url, $baseUploadsUrl) !== 0) {
		throw new RuntimeException('URL tidak valid');
	}
	$path = parse_url($url, PHP_URL_PATH) ?: '';
	$filename = basename($path);
	// Enforce naming pattern: 20 hex chars + .(jpg|png|gif|webp)
	if (!preg_match('~^[a-f0-9]{20}\.(jpg|png|gif|webp)$~i', $filename)) {
		throw new RuntimeException('Nama file tidak valid');
	}
	$fullPath = $UPLOAD_BASE_DIR . '/' . $filename;
	if (is_file($fullPath)) {
		@unlink($fullPath);
	}

	echo json_encode(['ok' => true]);
} catch (Throwable $e) {
	http_response_code(400);
	echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}

