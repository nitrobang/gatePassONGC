<?php
// Encryption function
require_once 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
function encrypt($data, $key) {
    $iv = random_bytes(16); // Generate a random IV (Initialization Vector)
    $cipherText = openssl_encrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $cipherText);
}

// Decryption function
function decrypt($encryptedData, $key) {
    $data = base64_decode($encryptedData);
    $iv = substr($data, 0, 16);
    $cipherText = substr($data, 16);
    return openssl_decrypt($cipherText, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
}

// Set the secret key (keep this secure)
$secretKey = $_ENV['SECRET_KEY'];
?>