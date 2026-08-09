<?php
include 'header.php'; // Pulls in database and security functions automatically

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Security Feature 2 Verification: SQL Injection protection via parameterized prepared statements
if ($search !== '') {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE title LIKE :search OR start_date LIKE :search");
    $stmt->execute(['search' => "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM projects");
}
$projects = $stmt->fetchAll();
?>

<h2>Public Project Directory</h2>

<!-- Search Form Component -->
<form action="index.php" method="GET" style="display: flex; gap: 10px; margin-bottom: 30px;">
    <!-- Security Feature 4 Applied: Escaping the input reflection value -->
    <input type="text" name="search" class="form-control" style="margin: 0;" value="<?= sanitize($search) ?>" placeholder="Search projects by title or start date...">
    <button type="submit" class="btn">Filter</button>
</form>

<div class="project-list">
    <?php if (empty($projects)): ?>
        <div class="card"><p>No software projects currently found matching that criteria.</p></div>
    <?php else: ?>
        <?php foreach ($projects as $project): ?>
            <div class="card">
                <!-- Security Feature 4 Applied: Sanitizing database data before print out -->
                <h3><?= sanitize($project['title']) ?></h3>
                <p style="color: #6c757d; font-size: 14px;"><strong>Target Start Date:</strong> <?= sanitize($project['start_date']) ?></p>
                <p><?= sanitize($project['short_description']) ?></p>
                <a href="project_details.php?pid=<?= intval($project['pid']) ?>" class="btn" style="background-color: #6c757d;">View Full Details</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
