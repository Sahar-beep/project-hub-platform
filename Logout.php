<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Verification: Wipe out all global session tokens entirely
$_SESSION = array();

// Remove tracking session cookies from local browser memory
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();
header("Location: index.php");
exit;
?>