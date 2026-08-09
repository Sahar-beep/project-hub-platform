<?php
include 'header.php';

// Security Feature 5: Authentication verification block
if (!isset($_SESSION['uid'])) {
    header("Location: login.php");
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : 'create';
$pid = isset($_GET['pid']) ? intval($_GET['pid']) : 0;

$title = $start_date = $end_date = $short_description = $phase = '';
$error = '';

if ($action === 'edit' && $pid > 0) {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE pid = ?");
    $stmt->execute([$pid]);
    $project = $stmt->fetch();
    
    if (!$project) {
        die("Target record was not found.");
    }
    
    // Security Feature 6: Strict Authorization Rule (Users can only modify their OWN records)
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
    // Security Feature 3 Verification: CSRF Token Validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Critical Error: Forged or expired CSRF session tokens detected.");
    }

    // Server-Side Form Validation & Trimming
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
            header("Location: dashboard.php");
            exit;
        } elseif ($action === 'edit' && $pid > 0) {
            // Re-verify authorization parameters directly inside the query execution criteria
            $upd = $pdo->prepare("UPDATE projects SET title = ?, start_date = ?, end_date = ?, short_description = ?, phase = ? WHERE pid = ? AND uid = ?");
            $upd->execute([$title, $start_date, $end_date, $short_description, $phase, $pid, $_SESSION['uid']]);
            header("Location: dashboard.php");
            exit;
        }
    }
}
?>

<div class="card" style="max-width: 650px; margin: 0 auto;">
    <h2><?= $action === 'edit' ? 'Update Project Parameters' : 'Publish New Enterprise Project' ?></h2>
    <?php if ($error): ?><div style="color: #dc3545; margin-bottom: 15px; font-weight: bold;"><?= sanitize($error) ?></div><?php endif; ?>

    <form action="project-action.php?action=<?= sanitize($action) ?>&pid=<?= $pid ?>" method="POST">
        <!-- Embedded Hidden Anti-CSRF Authentication Token -->
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <label>Project Title Name:</label>
        <input type="text" name="title" class="form-control" value="<?= sanitize($title) ?>" required>

        <div style="display: flex; gap: 20px;">
            <div style="flex: 1;">
                <label>Target Kickoff Date:</label>
                <input type="date" name="start_date" class="form-control" value="<?= sanitize($start_date) ?>" required>
            </div>
            <div style="flex: 1;">
                <label>Target Closure Date:</label>
                <input type="date" name="end_date" class="form-control" value="<?= sanitize($end_date) ?>" required>
            </div>
        </div>

        <label>Development Phase State:</label>
        <select name="phase" class="form-control" style="background:#fff; height:40px;" required>
            <option value="design" <?= $phase === 'design' ? 'selected' : '' ?>>Design Planning</option>
            <option value="development" <?= $phase === 'development' ? 'selected' : '' ?>>In Development</option>
            <option value="testing" <?= $phase === 'testing' ? 'selected' : '' ?>>Quality Assurance Testing</option>
            <option value="deployment" <?= $phase === 'deployment' ? 'selected' : '' ?>>System Deployment</option>
            <option value="complete" <?= $phase === 'complete' ? 'selected' : '' ?>>Complete</option>
        </select>

        <label>Short Technical Summary Description:</label>
        <textarea name="short_description" class="form-control" rows="5" style="height:auto;" required><?= sanitize($short_description) ?></textarea>

        <div style="margin-top: 10px; display: flex; gap: 10px;">
            <button type="submit" class="btn">Save Changes</button>
            <a href="dashboard.php" class="btn" style="background-color: #6c757d;">Cancel</a>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>