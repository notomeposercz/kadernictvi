<?php
// Databázové připojení
define('DB_HOST', 'db.dw189.webglobe.com');
define('DB_NAME', 'kader_myrec_cz');
define('DB_USER', 'kader_myrec_cz');
define('DB_PASS', 'aeiVSlBEMNvtcF0');

$pdo = null;

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5, // 5 sekund timeout
        PDO::ATTR_PERSISTENT => false
    ]);
} catch(PDOException $e) {
    // Loguj chybu místo die()
    error_log("Database connection error: " . $e->getMessage());
    $pdo = null; // Nech aplikaci běžet bez DB
}

// Nastavení pro session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Funkce pro kontrolu přihlášení
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Funkce pro kontrolu hesla
function verifyPassword($password) {
    return $password === 'kadernice2025';
}

// Funkce pro kontrolu připojení k databázi
function isDatabaseConnected() {
    global $pdo;
    return $pdo !== null;
}
?>