<?php
include 'Header.php';

// Security Feature 5: Authentication verification block
if (!isset($_SESSION['uid'])) {
    header("Location: Login.php");
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : 'create';
$pid = isset($_GET['pid']) ? intval($_GET['pid']) : 0;

$title = $start_date = $end_date = $short_description = $phase = '';
$error = '';

// --- NEW CODE: HANDLE DELETE ACTION ---
if ($action === 'delete' && $pid > 0) {
    // Security Feature 3 Verification: CSRF Token Validation for GET actions
    if (!isset($_GET['token']) || $_GET['token'] !== $_SESSION['csrf_token']) {
        die("Critical Error: Forged or expired CSRF session tokens detected.");
    }

    // First fetch the project to verify ownership
    $stmt = $pdo->prepare("SELECT uid FROM projects WHERE pid = ?");
    $stmt->execute([$pid]);
    $project = $stmt->fetch();

    if (!$project) {
        die("Target record was not found.");
    }

    // Security Feature 6: Strict Authorization Rule (Users can only delete their OWN records)
    if (intval($project['uid']) !== intval($_SESSION['uid'])) {
        die("Critical Authorization Failure: You do not possess ownership permissions for this project.");
    }

    // Execute secure parameterized deletion query
    $del = $pdo->prepare("DELETE FROM projects WHERE pid = ? AND uid = ?");
    $del->execute([$pid, $_SESSION['uid']]);
    
    header("Location: Dashboard.php");
    exit;
}
// --- END OF NEW DELETE CODE ---

if ($action === 'edit' && $pid > 0) {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE pid = ?");
    $stmt->execute([$pid]);
    $project = $stmt->fetch();
    
    if (!$project) {
        die("Target record was not found.");
    }
    
    if (intval($project['uid']) !== intval($_SESSION['uid'])) {
        die("Critical Authorization Failure: You do not possess ownership permissions for this project.");
    }

    $title = $project['title'];
    $start_date = $project['start_date'];
    $end_date = $project['end_date'];
    $short_description = $project['short_description'];
    $phase = $project['phase'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Critical Error: Forged or expired CSRF session tokens detected.");
    }

    $title = trim($_POST['title']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $short_description = trim($_POST['short_description']);
    $phase = $_POST['phase'];

    if (empty($title) || empty($start_date) || empty($end_date) || empty($short_description) || empty($phase)) {
        $error = "All layout fields require valid entries.";
    } else {
        if ($action === 'create') {
            $ins = $pdo->prepare("INSERT INTO projects (title, start_date, end_date, short_description, phase, uid) VALUES (?, ?, ?, ?, ?, ?)");
            $ins->execute([$title, $start_date, $end_date, $short_description, $phase, $_SESSION['uid']]);
            header("Location: Dashboard.php");
            exit;
        } elseif ($action === 'edit' && $pid > 0) {
            $upd = $pdo->prepare("UPDATE projects SET title = ?, start_date = ?, end_date = ?, short_description = ?, phase = ? WHERE pid = ? AND uid = ?");
            $upd->execute([$title, $start_date, $end_date, $short_description, $phase, $pid, $_SESSION['uid']]);
            header("Location: Dashboard.php");
            exit;
        }
    }
}
?>
<!-- Rest of your existing HTML form code remains unchanged below this -->
