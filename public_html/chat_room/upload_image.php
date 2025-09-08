<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';
require_login();

header('Content-Type: application/json');

try {
	if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
		throw new RuntimeException('Upload gagal');
	}
	$file = $_FILES['image'];
	$finfo = finfo_open(FILEINFO_MIME_TYPE);
	$mime = finfo_file($finfo, $file['tmp_name']);
	finfo_close($finfo);

	global $ALLOWED_IMAGE_MIME, $UPLOAD_BASE_DIR, $MAX_IMAGE_WIDTH, $MAX_IMAGE_HEIGHT, $JPEG_QUALITY, $APP_BASE_URL;
	if (!in_array($mime, $ALLOWED_IMAGE_MIME, true)) {
		throw new RuntimeException('Tipe file tidak diizinkan');
	}

	ensure_dir($UPLOAD_BASE_DIR);
	$ext = match ($mime) {
		'image/jpeg' => 'jpg',
		'image/png' => 'png',
		'image/gif' => 'gif',
		'image/webp' => 'webp',
		default => 'img'
	};
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

	$ok = false;
	if ($mime === 'image/jpeg') {
		$ok = imagejpeg($dst, $destPath, $JPEG_QUALITY);
	} elseif ($mime === 'image/png') {
		$ok = imagepng($dst, $destPath, 6);
	} elseif ($mime === 'image/gif') {
		$ok = imagegif($dst, $destPath);
	} elseif ($mime === 'image/webp') {
		$ok = imagewebp($dst, $destPath, $JPEG_QUALITY);
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