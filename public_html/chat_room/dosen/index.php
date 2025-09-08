<?php
declare(strict_types=1);
require_once __DIR__ . '/../config.php';
require_login();
require_role('dosen');
$pdo = get_pdo();

// Handle create/update/delete
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
		$errors[] = 'Invalid CSRF token';
	} else {
		$action = $_POST['action'] ?? '';
		try {
			if ($action === 'create') {
				$slug = preg_replace('~[^a-z0-9-]~', '-', strtolower(trim((string)($_POST['slug'] ?? ''))));
				$name = trim((string)($_POST['name'] ?? ''));
				if ($slug === '' || $name === '') throw new RuntimeException('Slug dan nama wajib diisi');
				$stmt = $pdo->prepare('INSERT INTO rooms (slug, name, created_by) VALUES (?, ?, ?)');
				$stmt->execute([$slug, $name, $_SESSION['user']['id']]);
			} elseif ($action === 'update') {
				$id = (int)($_POST['id'] ?? 0);
				$name = trim((string)($_POST['name'] ?? ''));
				if ($id <= 0 || $name === '') throw new RuntimeException('ID/Nama tidak valid');
				$stmt = $pdo->prepare('UPDATE rooms SET name = ? WHERE id = ?');
				$stmt->execute([$name, $id]);
			} elseif ($action === 'delete') {
				$id = (int)($_POST['id'] ?? 0);
				if ($id <= 0) throw new RuntimeException('ID tidak valid');
				// Get slug to optionally clean Firebase path (manual via moderation page)
				$pdo->prepare('DELETE FROM rooms WHERE id = ?')->execute([$id]);
			}
		} catch (Throwable $e) {
			$errors[] = 'Gagal menyimpan: ' . $e->getMessage();
		}
	}
}

$rooms = $pdo->query('SELECT id, slug, name, created_at FROM rooms ORDER BY created_at DESC')->fetchAll();

include __DIR__ . '/../partials/header.php';
?>

<div class="stack">
	<h2>Dashboard Dosen</h2>
	<?php if ($errors): ?>
		<div class="muted">
			<?php foreach ($errors as $er): ?>
				<div>- <?= htmlspecialchars($er) ?></div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<div class="card stack">
		<h3>Buat Room</h3>
		<form method="post" class="stack">
			<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
			<input type="hidden" name="action" value="create">
			<div class="form-row">
				<label>Slug (huruf kecil dan -)</label>
				<input type="text" name="slug" required placeholder="mis: kelas-if101">
			</div>
			<div class="form-row">
				<label>Nama Room</label>
				<input type="text" name="name" required placeholder="Kelas IF101">
			</div>
			<div class="actions">
				<button type="submit">Buat</button>
			</div>
		</form>
	</div>

	<div class="card stack">
		<h3>Daftar Room</h3>
		<?php if (!$rooms): ?>
			<div class="muted">Belum ada room</div>
		<?php else: ?>
			<div class="stack">
				<?php foreach ($rooms as $r): ?>
					<form method="post" class="actions" style="align-items:flex-end; gap:10px; flex-wrap:wrap;">
						<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
						<input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
						<div>
							<label>Slug</label>
							<input type="text" value="<?= htmlspecialchars($r['slug']) ?>" disabled>
						</div>
						<div>
							<label>Nama</label>
							<input type="text" name="name" value="<?= htmlspecialchars($r['name']) ?>">
						</div>
						<div class="actions">
							<button name="action" value="update" type="submit">Simpan</button>
							<button name="action" value="delete" type="submit" class="secondary" onclick="return confirm('Hapus room? Ini tidak menghapus data di Firebase.');">Hapus</button>
						</div>
					</form>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>

	<div class="card stack">
		<h3>Moderasi Pesan</h3>
		<p class="muted">Gunakan halaman moderasi untuk menghapus pesan kurang pantas per room.</p>
		<a class="btn" href="moderasi.php">Buka Moderasi</a>
	</div>

	<div class="card stack">
		<h3>Invite Link</h3>
		<p class="muted">Buat link invite untuk mahasiswa bergabung tanpa registrasi.</p>
		<a class="btn" href="invite.php">Buat Invite Link</a>
	</div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>

