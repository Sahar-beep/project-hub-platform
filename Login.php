<?php
include 'header.php';

if (isset($_SESSION['uid'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Security Verification: CSRF Token Integrity Check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF integrity check failed.");
    }

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        // SQL Injection Protection: Prepared Statement
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // Security Verification: Secure Password Hash Matching Check
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['uid'] = $user['uid'];
            $_SESSION['username'] = $user['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid validation parameter tokens provided.";
        }
    } else {
        $error = "Please fill in all layout parameters.";
    }
}
?>

<div class="card" style="max-width: 450px; margin: 40px auto;">
    <h2>Account Authorization Gateway</h2>
    <?php if ($error): ?><div style="color: #dc3545; margin-bottom: 15px; font-weight:bold;"><?= sanitize($error) ?></div><?php endif; ?>

    <form action="login.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <label>Username Handle:</label>
        <input type="text" name="username" class="form-control" required>

        <label>Security Key Password:</label>
        <input type="password" name="password" class="form-control" required>

        <button type="submit" class="btn" style="width: 100%; margin-top: 15px;">Authenticate Login</button>
    </form>
</div>

<?php include 'footer.php'; ?>