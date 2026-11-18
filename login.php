<?php
require __DIR__ . '/config.php';
require __DIR__ . '/crypto.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
csrf_check();

function fail(string $msg): void
{
    usleep(600000); // perlambat brute force
    $_SESSION['flash_error'] = $msg;
    header('Location: index.php');
    exit;
}

$sym = (string)($_POST['sym'] ?? '');
$priv = trim((string)($_POST['priv'] ?? ''));
if (!empty($_FILES['privfile']['tmp_name']) && is_uploaded_file($_FILES['privfile']['tmp_name'])) {
    if ($_FILES['privfile']['size'] > 10240) fail('File private key terlalu besar.');
    $priv = trim((string)file_get_contents($_FILES['privfile']['tmp_name']));
}
if ($sym === '' || $priv === '') fail('Kunci simetris dan private key wajib diisi.');
$priv = str_replace("\r\n", "\n", $priv) . "\n";

try {
    $row = db()->query('SELECT * FROM admin_keys WHERE id = 1')->fetch();
} catch (Throwable $ex) {
    fail('Database belum siap. Import database.sql terlebih dahulu.');
}
if (!$row) fail('Data kunci admin tidak ditemukan.');

// 1) verifikasi kunci simetris
if (!password_verify($sym, $row['sym_hash'])) fail('Kunci simetris salah.');
// 2) verifikasi kunci asimetris (private key harus berpasangan dengan public key tersimpan)
if (!asym_private_matches($priv, $row['public_key'])) fail('Private key tidak valid / tidak cocok.');

session_regenerate_id(true);
$_SESSION['admin'] = true;
$_SESSION['sym_key'] = base64_encode(sym_derive_key($sym, base64_decode($row['kdf_salt'])));
$_SESSION['priv_pem'] = $priv;
header('Location: admin.php');
