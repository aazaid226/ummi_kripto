<?php
require __DIR__ . '/config.php';
require __DIR__ . '/crypto.php';

if (empty($_SESSION['admin'])) { header('Location: index.php'); exit; }

$symKey = base64_decode($_SESSION['sym_key']);
$privPem = $_SESSION['priv_pem'];
$msg = '';
$err = '';

// Tambah data baru (dienkripsi sebelum disimpan)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $judul = trim($_POST['judul'] ?? '');
    $aes = trim($_POST['isi_aes'] ?? '');
    $rsa = trim($_POST['isi_rsa'] ?? '');
    if ($judul === '' || $aes === '' || $rsa === '') {
        $err = 'Semua kolom wajib diisi.';
    } elseif (strlen($rsa) > 150) {
        $err = 'Data RSA maksimal 150 karakter.';
    } else {
        $pub = db()->query('SELECT public_key FROM admin_keys WHERE id = 1')->fetchColumn();
        $st = db()->prepare('INSERT INTO data_terenkripsi (judul, isi_aes, isi_rsa) VALUES (?, ?, ?)');
        $st->execute([mb_substr($judul, 0, 100), sym_encrypt($aes, $symKey), asym_encrypt($rsa, $pub)]);
        $msg = 'Data berhasil dienkripsi dan disimpan.';
    }
}

$rows = db()->query('SELECT * FROM data_terenkripsi ORDER BY id')->fetchAll();
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Halaman Admin - SiKripto</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header><h1>Halaman Admin</h1><a href="logout.php">Logout</a></header>
<div class="wrap">
  <?php if ($msg): ?><div class="ok"><?= e($msg) ?></div><?php endif; ?>
  <?php if ($err): ?><div class="err"><?= e($err) ?></div><?php endif; ?>

  <h3>Konten Hasil Dekripsi</h3>
  <table>
    <tr><th>#</th><th>Judul</th><th>Isi (AES-256-GCM)</th><th>Isi (RSA-2048)</th></tr>
    <?php foreach ($rows as $i => $r):
        $a = sym_decrypt($r['isi_aes'], $symKey);
        $b = asym_decrypt($r['isi_rsa'], $privPem);
    ?>
    <tr>
      <td><?= $i + 1 ?></td>
      <td><?= e($r['judul']) ?></td>
      <td><span class="badge">Simetris</span> <?= $a === null ? '<em>gagal dekripsi</em>' : e($a) ?>
          <code class="ct" title="Ciphertext"><?= e($r['isi_aes']) ?></code></td>
      <td><span class="badge">Asimetris</span> <?= $b === null ? '<em>gagal dekripsi</em>' : e($b) ?>
          <code class="ct" title="Ciphertext"><?= e($r['isi_rsa']) ?></code></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$rows): ?><tr><td colspan="4">Belum ada data.</td></tr><?php endif; ?>
  </table>

  <h3 style="margin-top:32px">Tambah Data Terenkripsi</h3>
  <form method="post" class="card" style="margin:0;max-width:none">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <label>Judul</label><input type="text" name="judul" maxlength="100" required>
    <label>Isi (dienkripsi AES-256-GCM)</label><textarea name="isi_aes" style="min-height:70px" required></textarea>
    <label>Isi rahasia (dienkripsi RSA-2048, maks. 150 karakter)</label><input type="text" name="isi_rsa" maxlength="150" required>
    <button type="submit">Enkripsi &amp; Simpan</button>
  </form>
</div>
</body>
</html>
