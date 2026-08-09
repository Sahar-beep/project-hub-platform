<?php
// Ensure db.php is always included first to keep session settings uniform
require_once 'db.php';

// Security Feature 3: Cross-Site Request Forgery (CSRF) Mitigation Token Generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); 
}

// Security Feature 4: Cross-Site Scripting (XSS) & HTML Injection Protection Utility
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Hub Platform</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a class="navbar-brand" href="index.php">🚀 ProjectHub</a>
            <ul class="navbar-menu">
                <li><a class="navbar-link" href="index.php">Public Directory</a></li>
                <?php if (isset($_SESSION['uid'])): ?>
                    <li><a class="navbar-link" href="dashboard.php">Dashboard</a></li>
                    <li><a class="navbar-link btn btn-danger" href="Logout.php">Logout (<?= sanitize($_SESSION['username']) ?>)</a></li>
                <?php else: ?>
                    <li><a class="navbar-link" href="login.php">Login</a></li>
                    <li><a class="navbar-link btn" href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    <main class="container">