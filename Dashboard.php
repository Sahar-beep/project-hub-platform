<?php
include 'Header.php';

// Security Verification: Session authentication verification gate
if (!isset($_SESSION['uid'])) {
    header("Location: Login.php");
    exit;
}

// SQL Injection Protection: Select only projects belonging to the current user
$stmt = $pdo->prepare("SELECT * FROM projects WHERE uid = ?");
$stmt->execute([$_SESSION['uid']]);
$my_projects = $stmt->fetchAll();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <h2>Developer Dashboard Workspace</h2>
    <a href="Project_Action.php?action=create" class="btn">＋ Create New Project</a>
</div>

<div class="project-list">
    <?php if (empty($my_projects)): ?>
        <div class="card"><p>You haven't registered any project tracks to your account profile yet.</p></div>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <thead>
                <tr style="background: #212529; color: white; text-align: left;">
                    <th style="padding: 15px;">Project Name</th>
                    <th style="padding: 15px;">Current Phase</th>
                    <th style="padding: 15px; text-align: right;">Action Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($my_projects as $project): ?>
                    <tr style="border-bottom: 1px solid #dee2e6;">
                        <td style="padding: 15px; font-weight: bold;"><?= sanitize($project['title']) ?></td>
                        <td style="padding: 15px;"><span style="text-transform:uppercase; background:#e9ecef; padding:4px 8px; border-radius:4px; font-size:12px; font-weight:bold;"><?= sanitize($project['phase']) ?></span></td>
                        
                        <!-- UPDATED ACTIONS COLUMN WITH INLINE FLEXBOX AND DELETE ACTION -->
                        <td style="padding: 15px; text-align: right; display: flex; gap: 10px; justify-content: flex-end; align-items: center;">
                            <!-- Edit Button Vector Link -->
                            <a href="Project_Action.php?action=edit&pid=<?= intval($project['pid']) ?>" class="btn" style="padding: 5px 12px; font-size: 14px; background-color: #0d6efd;">Modify Settings</a>
                            
                            <!-- Secure Deletion Link with anti-CSRF token verification and confirmation box -->
                            <a href="Project_Action.php?action=delete&pid=<?= intval($project['pid']) ?>&token=<?= $_SESSION['csrf_token'] ?>" 
                               class="btn btn-danger" 
                               style="padding: 5px 12px; font-size: 14px;" 
                               onclick="return confirm('Are you completely sure you want to permanently erase this project track?');">
                               Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include 'Footer.php'; ?>
