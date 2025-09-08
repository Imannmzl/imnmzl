<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

// Allow both regular users and session users
if (empty($_SESSION['user']) && empty($_SESSION['session_user'])) {
	http_response_code(401);
	echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
	exit;
}

header('Content-Type: application/json');

try {
	if (!isset($_FILES['image'])) {
		throw new RuntimeException('No file uploaded');
	}
	
	$file = $_FILES['image'];
	
	// Check for upload errors
	if ($file['error'] !== UPLOAD_ERR_OK) {
		$error_messages = [
			UPLOAD_ERR_INI_SIZE => 'File terlalu besar (server limit)',
			UPLOAD_ERR_FORM_SIZE => 'File terlalu besar (form limit)',
			UPLOAD_ERR_PARTIAL => 'File hanya ter-upload sebagian',
			UPLOAD_ERR_NO_FILE => 'Tidak ada file yang diupload',
			UPLOAD_ERR_NO_TMP_DIR => 'Temporary directory tidak ada',
			UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file',
			UPLOAD_ERR_EXTENSION => 'Upload dihentikan oleh extension'
		];
		$error_msg = $error_messages[$file['error']] ?? 'Upload error: ' . $file['error'];
		throw new RuntimeException($error_msg);
	}
	
	// Check file size (max 7MB)
	if ($file['size'] > 7 * 1024 * 1024) {
		throw new RuntimeException('File terlalu besar (max 7MB)');
	}
	$finfo = finfo_open(FILEINFO_MIME_TYPE);
	$mime = finfo_file($finfo, $file['tmp_name']);
	finfo_close($finfo);

	global $ALLOWED_IMAGE_MIME, $UPLOAD_BASE_DIR, $MAX_IMAGE_WIDTH, $MAX_IMAGE_HEIGHT, $JPEG_QUALITY, $PNG_COMPRESSION, $WEBP_QUALITY, $APP_BASE_URL;
	if (!in_array($mime, $ALLOWED_IMAGE_MIME, true)) {
		throw new RuntimeException('Tipe file tidak diizinkan');
	}

	ensure_dir($UPLOAD_BASE_DIR);
	
	// Check if upload directory is writable
	if (!is_writable($UPLOAD_BASE_DIR)) {
		throw new RuntimeException('Upload directory tidak writable');
	}
	
	// Convert all images to WebP for better compression (except if already WebP)
	$ext = 'webp';
	$filename = bin2hex(random_bytes(10)) . '.' . $ext;
	$destPath = $UPLOAD_BASE_DIR . '/' . $filename;

	// Load and downscale using GD
	[$width, $height] = getimagesize($file['tmp_name']);
	$scale = min(1.0, min($MAX_IMAGE_WIDTH / max(1,$width), $MAX_IMAGE_HEIGHT / max(1,$height)));
	$newW = max(1, (int)floor($width * $scale));
	$newH = max(1, (int)floor($height * $scale));

	switch ($mime) {
		case 'image/jpeg': $src = imagecreatefromjpeg($file['tmp_name']); break;
		case 'image/png': $src = imagecreatefrompng($file['tmp_name']); imagesavealpha($src, true); break;
		case 'image/gif': $src = imagecreatefromgif($file['tmp_name']); break;
		case 'image/webp': $src = imagecreatefromwebp($file['tmp_name']); break;
		default: throw new RuntimeException('Tipe tidak didukung');
	}
	$dst = imagecreatetruecolor($newW, $newH);
	// Preserve alpha for PNG/WebP
	if (in_array($mime, ['image/png', 'image/webp'], true)) {
		imagealphablending($dst, false);
		imagesavealpha($dst, true);
	}
	imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $width, $height);

	// Convert to WebP for optimal web compression (with fallback)
	$ok = false;
	if (function_exists('imagewebp')) {
		$ok = imagewebp($dst, $destPath, $WEBP_QUALITY);
	} else {
		// Fallback to JPEG if WebP not supported
		$ext = 'jpg';
		$filename = bin2hex(random_bytes(10)) . '.' . $ext;
		$destPath = $UPLOAD_BASE_DIR . '/' . $filename;
		imageinterlace($dst, true);
		$ok = imagejpeg($dst, $destPath, $JPEG_QUALITY);
	}
	imagedestroy($src);
	imagedestroy($dst);

	if (!$ok) {
		throw new RuntimeException('Gagal menyimpan gambar');
	}

	$baseUrl = rtrim($APP_BASE_URL, '/');
	$url = $baseUrl . '/uploads/' . rawurlencode($filename);

echo json_encode(['ok' => true, 'url' => $url]);
} catch (Throwable $e) {
	http_response_code(400);
	echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}