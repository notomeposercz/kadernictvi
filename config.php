<?php
// Databázové připojení
define('DB_HOST', 'db.dw189.webglobe.com');
define('DB_NAME', 'kader_myrec_cz');
define('DB_USER', 'kader_myrec_cz'); // změňte na své údaje
define('DB_PASS', 'aeiVSlBEMNvtcF0'); // změňte na své údaje

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Chyba připojení k databázi: " . $e->getMessage());
}

// Nastavení pro session
session_start();

// Funkce pro kontrolu přihlášení
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Funkce pro kontrolu hesla
function verifyPassword($password) {
    return $password === 'kadernice2025'; // nebo použijte hash_verify() pro bezpečnější řešení
}
?>