<?php
/**
 * Fungsi kriptografi.
 *  - Simetris   : AES-256-GCM, kunci diturunkan dari passphrase via PBKDF2-SHA256 (100.000 iterasi)
 *  - Asimetris  : RSA-2048, enkripsi RSA-OAEP (public key), dekripsi dengan private key
 */

function sym_derive_key(string $passphrase, string $salt): string
{
    return hash_pbkdf2('sha256', $passphrase, $salt, 100000, 32, true);
}

function sym_encrypt(string $plain, string $key): string
{
    $iv = random_bytes(12);
    $tag = '';
    $ct = openssl_encrypt($plain, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag, '', 16);
    return base64_encode($iv . $tag . $ct);
}

function sym_decrypt(string $b64, string $key): ?string
{
    $raw = base64_decode($b64, true);
    if ($raw === false || strlen($raw) < 28) return null;
    $iv = substr($raw, 0, 12);
    $tag = substr($raw, 12, 16);
    $ct = substr($raw, 28);
    $out = openssl_decrypt($ct, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
    return $out === false ? null : $out;
}

function asym_encrypt(string $plain, string $publicPem): string
{
    $ct = '';
    if (!openssl_public_encrypt($plain, $ct, $publicPem, OPENSSL_PKCS1_OAEP_PADDING)) {
        throw new RuntimeException('Enkripsi RSA gagal (data terlalu panjang? maks ~190 byte).');
    }
    return base64_encode($ct);
}

function asym_decrypt(string $b64, string $privatePem): ?string
{
    $raw = base64_decode($b64, true);
    if ($raw === false) return null;
    $out = '';
    return openssl_private_decrypt($raw, $out, $privatePem, OPENSSL_PKCS1_OAEP_PADDING) ? $out : null;
}

/** Cek private key cocok dengan public key yang tersimpan (uji tanda tangan digital). */
function asym_private_matches(string $privatePem, string $publicPem): bool
{
    $priv = @openssl_pkey_get_private($privatePem);
    if (!$priv) return false;
    $challenge = random_bytes(32);
    $sig = '';
    if (!openssl_sign($challenge, $sig, $priv, OPENSSL_ALGO_SHA256)) return false;
    return openssl_verify($challenge, $sig, $publicPem, OPENSSL_ALGO_SHA256) === 1;
}
