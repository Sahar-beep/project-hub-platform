<?php
include 'header.php';

if (isset($_SESSION['uid'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF validation failure.");
    }

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($email) && !empty($password)) {
        // SQL Injection Protection: Prepared Statement to check uniqueness
        $chk = $pdo->prepare("SELECT uid FROM users WHERE username = ? OR email = ?");
        $chk->execute([$username, $email]);
        
        if ($chk->fetch()) {
            $error = "The chosen username profile or mailbox entry already exists.";
        } else {
            // Security Verification: Secure, native password hashing engine
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $ins = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
            $ins->execute([$username, $hashedPassword, $email]);
            $success = "Registration profile generated. You can now login safely.";
        }
    } else {
        $error = "All structural credential boxes require values.";
    }
}
?>

<div class="card" style="max-width: 450px; margin: 40px auto;">
    <h2>Register New User Account</h2>
    <?php if ($error): ?><div style="color: #dc3545; margin-bottom: 15px; font-weight:bold;"><?= sanitize($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div style="color: #198754; margin-bottom: 15px; font-weight:bold;"><?= sanitize($success) ?></div><?php endif; ?>

    <form action="register.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <label>Account Handle Username:</label>
        <input type="text" name="username" class="form-control" required>

        <label>Corporate E-mail Target:</label>
        <input type="email" name="email" class="form-control" required>

        <label>Access Key Password:</label>
        <input type="password" name="password" class="form-control" required>

        <button type="submit" class="btn" style="width: 100%; margin-top: 15px;">Create Secure Account</button>
    </form>
</div>

<?php include 'footer.php'; ?>