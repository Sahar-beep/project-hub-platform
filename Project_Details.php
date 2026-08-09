<?php
include 'header.php';

// Data Validation: Force project ID query parameters to be strict integers
$pid = isset($_GET['pid']) ? intval($_GET['pid']) : 0;

// Security Feature 2 Verification: Secure positional placeholder prepared statement
$stmt = $pdo->prepare("SELECT p.*, u.email FROM projects p JOIN users u ON p.uid = u.uid WHERE p.pid = ?");
$stmt->execute([$pid]);
$project = $stmt->fetch();

if (!$project) {
    echo "<div class='card'><p>The requested project record could not be found.</p></div>";
    include 'footer.php';
    exit;
}
?>

<div class="card">
    <!-- Security Feature 4 Applied: Cleaned database content delivery -->
    <h2><?= sanitize($project['title']) ?></h2>
    <hr style="border: 0; border-top: 1px solid #dee2e6; margin: 20px 0;">
    
    <p><strong>Operational Phase:</strong> <span style="text-transform: uppercase; background: #e9ecef; padding: 3px 8px; border-radius: 4px; font-size: 14px; font-weight: bold;"><?= sanitize($project['phase']) ?></span></p>
    <p><strong>Timeline Limits:</strong> <?= sanitize($project['start_date']) ?> — To — <?= sanitize($project['end_date']) ?></p>
    <p><strong>Lead Developer Contact:</strong> <a href="mailto:<?= sanitize($project['email']) ?>"><?= sanitize($project['email']) ?></a></p>
    
    <div style="margin-top: 20px; background-color: #f8f9fa; padding: 15px; border-radius: 4px;">
        <h5>Project Objective Overview:</h5>
        <p style="margin: 0; line-height: 1.6;"><?= sanitize($project['short_description']) ?></p>
    </div>
    
    <a href="index.php" class="btn" style="background-color: #212529; margin-top: 20px;">Return to Directory</a>
</div>

<?php include 'footer.php'; ?>