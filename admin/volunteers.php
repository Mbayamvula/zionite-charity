<?php
/**
 * Zionite Charity - Admin Volunteers CRUD
 * Full CRUD operations for volunteers management
 */

require_once '../includes/config.php';

requireAdminLogin();

$message = '';
$messageType = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_status') {
        $id = intval($_POST['id'] ?? 0);
        $status = sanitize($_POST['status'] ?? 'pending');
        
        try {
            $stmt = $pdo->prepare("UPDATE volunteers SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
            $message = 'Volunteer status updated successfully!';
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'Database error: ' . $e->getMessage();
            $messageType = 'error';
        }
    } elseif ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        try {
            $stmt = $pdo->prepare("DELETE FROM volunteers WHERE id = ?");
            $stmt->execute([$id]);
            $message = 'Volunteer deleted successfully!';
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'Database error: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Fetch all volunteers
try {
    $stmt = $pdo->query("SELECT * FROM volunteers ORDER BY created_at DESC");
    $volunteers = $stmt->fetchAll();
} catch (PDOException $e) {
    $volunteers = [];
}

// Fetch volunteer for viewing details
$viewVolunteer = null;
if (isset($_GET['view'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM volunteers WHERE id = ?");
        $stmt->execute([intval($_GET['view'])]);
        $viewVolunteer = $stmt->fetch();
    } catch (PDOException $e) {
        $viewVolunteer = null;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteers | Zionite Charity Admin</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    
    <style>
        body {
            background: var(--off-white);
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--gray-200);
        }
        
        .btn-view {
            background: #48bb78;
            color: var(--white);
            padding: 5px 10px;
            border-radius: 3px;
            text-decoration: none;
            font-size: 0.875rem;
        }
        
        .btn-delete {
            background: #f56565;
            color: var(--white);
            padding: 5px 10px;
            border-radius: 3px;
            text-decoration: none;
            font-size: 0.875rem;
        }
        
        .detail-modal {
            background: var(--white);
            padding: 30px;
            border-radius: 10px;
            box-shadow: var(--shadow-lg);
            margin-bottom: 30px;
        }
        
        .detail-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid var(--gray-200);
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 600;
            width: 150px;
            color: var(--gray-700);
        }
        
        .detail-value {
            flex: 1;
            color: var(--gray-600);
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <h2><i class="fas fa-hands-holding-heart"></i> Admin</h2>
            
            <nav class="admin-nav">
                <ul>
                    <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="projects.php"><i class="fas fa-project-diagram"></i> Projects</a></li>
                    <li><a href="reports.php"><i class="fas fa-file-alt"></i> Reports</a></li>
                    <li><a href="volunteers.php" class="active"><i class="fas fa-users"></i> Volunteers</a></li>
                    <li><a href="donations.php"><i class="fas fa-donate"></i> Donations</a></li>
                    <li><a href="partners.php"><i class="fas fa-handshake"></i> Partners</a></li>
                    <li><a href="messages.php"><i class="fas fa-envelope"></i> Messages</a></li>
                    <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Website</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-content">
            <div class="admin-header">
                <h1>Volunteers Management</h1>
            </div>
            
            <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>" style="margin-bottom: 25px;">
                <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>
            
            <?php if ($viewVolunteer): ?>
            <!-- Volunteer Details Modal -->
            <div class="detail-modal">
                <h2>Volunteer Details</h2>
                <div class="detail-row">
                    <div class="detail-label">Name:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($viewVolunteer['first_name'] . ' ' . $viewVolunteer['last_name']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Email:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($viewVolunteer['email']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Phone:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($viewVolunteer['phone']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Address:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($viewVolunteer['address']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">City:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($viewVolunteer['city']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Country:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($viewVolunteer['country']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Skills:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($viewVolunteer['skills']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Availability:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($viewVolunteer['availability']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Motivation:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($viewVolunteer['motivation']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Status:</div>
                    <div class="detail-value">
                        <span class="badge badge-<?php echo $viewVolunteer['status']; ?>"><?php echo ucfirst($viewVolunteer['status']); ?></span>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Applied On:</div>
                    <div class="detail-value"><?php echo formatDate($viewVolunteer['created_at']); ?></div>
                </div>
                
                <div style="margin-top: 20px;">
                    <form method="POST" action="" style="display: inline;">
                        <?php echo csrfField(); ?>
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?php echo $viewVolunteer['id']; ?>">
                        <select name="status" style="padding: 8px; border-radius: 5px; border: 2px solid var(--gray-200);">
                            <option value="pending" <?php echo $viewVolunteer['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="approved" <?php echo $viewVolunteer['status'] == 'approved' ? 'selected' : ''; ?>>Approved</option>
                            <option value="rejected" <?php echo $viewVolunteer['status'] == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                        </select>
                        <button type="submit" class="btn btn-donate" style="padding: 8px 15px;">Update Status</button>
                    </form>
                    <a href="volunteers.php" class="btn btn-secondary" style="margin-left: 10px;">Close</a>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Volunteers Table -->
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Location</th>
                            <th>Skills</th>
                            <th>Status</th>
                            <th>Applied On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($volunteers as $volunteer): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($volunteer['first_name'] . ' ' . $volunteer['last_name']); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($volunteer['email']); ?></td>
                            <td><?php echo htmlspecialchars($volunteer['phone']); ?></td>
                            <td>
                                <?php echo htmlspecialchars($volunteer['city']); ?>
                                <?php if ($volunteer['country']): ?>
                                , <?php echo htmlspecialchars($volunteer['country']); ?>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars(substr($volunteer['skills'], 0, 30)); ?>...</td>
                            <td>
                                <span class="badge badge-<?php echo $volunteer['status']; ?>"><?php echo ucfirst($volunteer['status']); ?></span>
                            </td>
                            <td><?php echo formatDate($volunteer['created_at']); ?></td>
                            <td>
                                <a href="?view=<?php echo $volunteer['id']; ?>" class="btn-view"><i class="fas fa-eye"></i> View</a>
                                <form method="POST" action="" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this volunteer?');">
                                    <?php echo csrfField(); ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $volunteer['id']; ?>">
                                    <button type="submit" class="btn-delete"><i class="fas fa-trash"></i> Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
