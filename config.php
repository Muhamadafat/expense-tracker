<?php
// Konfigurasi Database
// Gunakan environment variables untuk Railway, fallback ke localhost untuk development
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'expense_tracker');

// Railway biasanya menyediakan MYSQLDATABASE, MYSQLHOST, dll
if (getenv('MYSQLHOST')) {
    define('RAILWAY_DB_HOST', getenv('MYSQLHOST'));
    define('RAILWAY_DB_USER', getenv('MYSQLUSER'));
    define('RAILWAY_DB_PASS', getenv('MYSQLPASSWORD'));
    define('RAILWAY_DB_NAME', getenv('MYSQLDATABASE'));
    define('RAILWAY_DB_PORT', getenv('MYSQLPORT') ?: 3306);
}

// Koneksi ke database
function getDBConnection() {
    // Cek apakah ada Railway environment variables
    if (defined('RAILWAY_DB_HOST')) {
        $conn = new mysqli(RAILWAY_DB_HOST, RAILWAY_DB_USER, RAILWAY_DB_PASS, RAILWAY_DB_NAME, RAILWAY_DB_PORT);
    } else {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    }

    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");
    return $conn;
}

// Fungsi untuk sanitasi input
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Set timezone
date_default_timezone_set('Asia/Jakarta');
?>