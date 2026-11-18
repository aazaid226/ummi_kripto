<?php
/**
 * Generator kunci + database.sql (jalankan sekali dari CLI):
 *   php tools/generate.php
 * Hasil:
 *   kunci/kunci_simetris.txt   -> passphrase untuk login
 *   kunci/private_key.pem      -> kunci privat RSA untuk login
 *   database.sql               -> struktur + data terenkripsi (import ke MySQL)
 */
require __DIR__ . '/../crypto.php';

$root = dirname(__DIR__);
@mkdir($root . '/kunci', 0755, true);

$passphrase = 'UMMI-' . strtoupper(bin2hex(random_bytes(6)));
$salt = random_bytes(16);
$symKey = sym_derive_key($passphrase, $salt);

$res = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
openssl_pkey_export($res, $privPem);
$publicPem = openssl_pkey_get_details($res)['key'];

file_put_contents($root . '/kunci/kunci_simetris.txt', $passphrase . "\n");
file_put_contents($root . '/kunci/private_key.pem', $privPem);

// Data contoh "hasil enkripsi dari penyimpanan data sebelumnya"
$records = [
    ['Data Mahasiswa 1', 'Nama: Ahmad Fauzi | NIM: 2211001 | Prodi: Informatika', 'Nilai Keamanan Informasi: A'],
    ['Data Mahasiswa 2', 'Nama: Siti Aisyah | NIM: 2211002 | Prodi: Sistem Informasi', 'Nilai Keamanan Informasi: A-'],
    ['Data Mahasiswa 3', 'Nama: Budi Santoso | NIM: 2211003 | Prodi: Informatika', 'Nilai Keamanan Informasi: B+'],
    ['Catatan Rahasia', 'Rapat evaluasi kurikulum dijadwalkan Jumat pukul 13.00', 'Kode akses ruang server: 7391-AX'],
];

$q = fn(string $s) => "'" . str_replace(["\\", "'"], ["\\\\", "''"], $s) . "'";

$sql = "-- Database untuk tugas: Sistem Informasi PHP dengan login kunci simetris & asimetris\n";
$sql .= "CREATE DATABASE IF NOT EXISTS ummi_kripto CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\n";
$sql .= "USE ummi_kripto;\n\n";
$sql .= "DROP TABLE IF EXISTS data_terenkripsi;\nDROP TABLE IF EXISTS admin_keys;\n\n";
$sql .= "CREATE TABLE admin_keys (\n  id INT PRIMARY KEY,\n  sym_hash VARCHAR(255) NOT NULL COMMENT 'password_hash dari kunci simetris',\n  kdf_salt VARCHAR(64) NOT NULL COMMENT 'salt PBKDF2 (base64)',\n  public_key TEXT NOT NULL COMMENT 'RSA public key (PEM)'\n) ENGINE=InnoDB;\n\n";
$sql .= "CREATE TABLE data_terenkripsi (\n  id INT AUTO_INCREMENT PRIMARY KEY,\n  judul VARCHAR(100) NOT NULL,\n  isi_aes TEXT NOT NULL COMMENT 'AES-256-GCM (base64)',\n  isi_rsa TEXT NOT NULL COMMENT 'RSA-2048 OAEP (base64)',\n  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n) ENGINE=InnoDB;\n\n";
$sql .= "INSERT INTO admin_keys (id, sym_hash, kdf_salt, public_key) VALUES (1, "
    . $q(password_hash($passphrase, PASSWORD_DEFAULT)) . ', ' . $q(base64_encode($salt)) . ', ' . $q($publicPem) . ");\n\n";
foreach ($records as [$judul, $aes, $rsa]) {
    $sql .= "INSERT INTO data_terenkripsi (judul, isi_aes, isi_rsa) VALUES ("
        . $q($judul) . ', ' . $q(sym_encrypt($aes, $symKey)) . ', ' . $q(asym_encrypt($rsa, $publicPem)) . ");\n";
}
file_put_contents($root . '/database.sql', $sql);

echo "Selesai.\nKunci simetris : $passphrase\nPrivate key    : kunci/private_key.pem\nDatabase       : database.sql\n";
