<?php
// Security Feature 1: Session Cookie Protection & Defense-in-Depth
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);     // Prevents JavaScript from stealing session cookies
    ini_set('session.cookie_use_only_cookies', 1); // Forces sessions to only use cookies, not URLs
    ini_set('session.use_strict_mode', 1);     // Prevents session fixation attacks
    session_start();
}

$host    = '127.0.0.1'; // Using IP instead of 'localhost' bypasses DNS lookup for performance
$db      = 'aproject_db';
$user    = 'root'; 
$pass    = '';     
$charset = 'utf8mb4';

// Security Feature 2: SQL Injection Protection (Enforces true native prepared statements)
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false, // Disables emulated prepared statements
];

try {
     $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass, $options);
} catch (\PDOException $e) {
     // Secure logging protocol hides raw database credentials from public viewers
     error_log("Database Infrastructure Error: " . $e->getMessage());
     die("Critical Error: Core application database infrastructure connection failure.");
}
?>