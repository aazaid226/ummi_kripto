# SiKripto – Sistem Informasi PHP dengan Login Kunci Simetris & Asimetris

Landing page → login dengan **input kunci** (bukan username/password) → halaman admin yang menampilkan
seluruh data hasil enkripsi (didekripsi memakai kunci yang diinput).

## Cara menjalankan (XAMPP / Laragon)
1. Salin folder `ummi_kripto` ke `htdocs` (XAMPP) atau `www` (Laragon).
2. Jalankan Apache + MySQL, buka phpMyAdmin → **Import** file `database.sql`
   (otomatis membuat database `ummi_kripto`).
3. Jika user/password MySQL bukan `root` / kosong, ubah di `config.php`.
4. Buka `http://localhost/ummi_kripto/`.
5. Login memakai:
   - **Kunci simetris**: isi `kunci/kunci_simetris.txt`
   - **Kunci asimetris**: isi `kunci/private_key.pem` (tempel atau unggah file)

> Ekstensi PHP yang dibutuhkan: `openssl`, `pdo_mysql` (aktif secara default di XAMPP).

## Alur & kriptografi
| Bagian | Algoritma |
|---|---|
| Kunci simetris | AES-256-GCM, kunci diturunkan dari passphrase dengan PBKDF2-SHA256 (100.000 iterasi) |
| Kunci asimetris | RSA-2048, enkripsi RSA-OAEP (public key), dekripsi dengan private key |
| Verifikasi login simetris | `password_verify` terhadap hash passphrase di tabel `admin_keys` |
| Verifikasi login asimetris | Private key menandatangani tantangan acak, diverifikasi dengan public key di DB |

Tabel `data_terenkripsi`: kolom `isi_aes` (dienkripsi simetris) dan `isi_rsa` (dienkripsi asimetris).
Admin hanya bisa membaca kedua kolom jika **kedua kunci benar**. Admin juga bisa menambah data baru
yang langsung dienkripsi sebelum disimpan.

## Struktur file
- `index.php` – landing page + form input kunci
- `login.php` – validasi kunci simetris & asimetris
- `admin.php` – halaman admin (dekripsi + tambah data)
- `logout.php` – keluar
- `crypto.php` – fungsi enkripsi/dekripsi
- `config.php` – koneksi DB & session
- `database.sql` – struktur tabel + data terenkripsi contoh
- `tools/generate.php` – membuat ulang kunci & `database.sql` (`php tools/generate.php`)
- `kunci/` – kunci untuk login (di produksi jangan disimpan di dalam web root)

## Keamanan yang diterapkan
Prepared statement (anti SQL injection), escape output (anti XSS), token CSRF, regenerasi session ID,
jeda saat login gagal.
