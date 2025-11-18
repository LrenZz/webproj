<?php
// inc/config.php
session_start();

// change these to your DB credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'barangay_blotter');
define('DB_USER', 'root');
define('DB_PASS', ''); // set your mysql password

try {
    $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (Exception $e) {
    die('Database connection error: '.$e->getMessage());
}
