<?php
require __DIR__ . '/config.php';
if (!empty($_SESSION['admin'])) { header('Location: admin.php'); exit; }
$error = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_error']);
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>SiKripto - Landing Page</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header><h1>SiKripto UMMI</h1></header>
<section class="hero">
  <h2>Sistem Informasi Data Terenkripsi</h2>
  <p>Masuk ke halaman admin menggunakan kunci simetris dan kunci asimetris (RSA).</p>
</section>

<main class="card">
  <h3 style="margin-top:0">Login Admin (Input Kunci)</h3>
  <?php if ($error): ?><div class="err"><?= e($error) ?></div><?php endif; ?>
  <form method="post" action="login.php" enctype="multipart/form-data" autocomplete="off">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

    <label for="sym">Kunci Simetris (passphrase AES-256)</label>
    <input type="password" id="sym" name="sym" required>

    <label for="priv">Kunci Asimetris - Private Key RSA (PEM)</label>
    <textarea id="priv" name="priv" placeholder="-----BEGIN PRIVATE KEY-----&#10;...&#10;-----END PRIVATE KEY-----"></textarea>
    <div class="hint">Tempel isi file private key, atau unggah file .pem di bawah.</div>

    <label for="privfile">Atau unggah file private key</label>
    <input type="file" id="privfile" name="privfile" accept=".pem,.key,.txt">

    <button type="submit">Masuk ke Halaman Admin</button>
  </form>
</main>
</body>
</html>
