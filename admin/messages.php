<?php
/**
 * Zionite Charity - Admin Messages Page
 * View and manage contact messages
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
        $status = sanitize($_POST['status'] ?? 'unread');
        
        try {
            $stmt = $pdo->prepare("UPDATE contact_messages SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
            $message = 'Message status updated successfully!';
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'Database error: ' . $e->getMessage();
            $messageType = 'error';
        }
    } elseif ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        try {
            $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
            $stmt->execute([$id]);
            $message = 'Message deleted successfully!';
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'Database error: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Fetch all messages
try {
    $stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
    $messages = $stmt->fetchAll();
} catch (PDOException $e) {
    $messages = [];
}

// Fetch message for viewing details
$viewMessage = null;
if (isset($_GET['view'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE id = ?");
        $stmt->execute([intval($_GET['view'])]);
        $viewMessage = $stmt->fetch();
        
        // Mark as read if unread
        if ($viewMessage && $viewMessage['status'] == 'unread') {
            $updateStmt = $pdo->prepare("UPDATE contact_messages SET status = 'read' WHERE id = ?");
            $updateStmt->execute([$viewMessage['id']]);
        }
    } catch (PDOException $e) {
        $viewMessage = null;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages | Zionite Charity Admin</title>
    
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
            width: 120px;
            color: var(--gray-700);
        }
        
        .detail-value {
            flex: 1;
            color: var(--gray-600);
        }
        
        .message-content {
            background: var(--off-white);
            padding: 20px;
            border-radius: 8px;
            margin-top: 15px;
            line-height: 1.8;
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
                    <li><a href="volunteers.php"><i class="fas fa-users"></i> Volunteers</a></li>
                    <li><a href="donations.php"><i class="fas fa-donate"></i> Donations</a></li>
                    <li><a href="partners.php"><i class="fas fa-handshake"></i> Partners</a></li>
                    <li><a href="messages.php" class="active"><i class="fas fa-envelope"></i> Messages</a></li>
                    <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Website</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-content">
            <div class="admin-header">
                <h1>Contact Messages</h1>
            </div>
            
            <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>" style="margin-bottom: 25px;">
                <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>
            
            <?php if ($viewMessage): ?>
            <!-- Message Details Modal -->
            <div class="detail-modal">
                <h2>Message Details</h2>
                <div class="detail-row">
                    <div class="detail-label">From:</div>
                    <div class="detail-value">
                        <strong><?php echo htmlspecialchars($viewMessage['name']); ?></strong>
                        <?php if ($viewMessage['email']): ?>
                        <br><small><?php echo htmlspecialchars($viewMessage['email']); ?></small>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Phone:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($viewMessage['phone']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Subject:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($viewMessage['subject']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Status:</div>
                    <div class="detail-value">
                        <span class="badge badge-<?php echo $viewMessage['status']; ?>"><?php echo ucfirst($viewMessage['status']); ?></span>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Received:</div>
                    <div class="detail-value"><?php echo formatDate($viewMessage['created_at']); ?></div>
                </div>
                
                <div class="message-content">
                    <strong>Message:</strong><br>
                    <?php echo nl2br(htmlspecialchars($viewMessage['message'])); ?>
                </div>
                
                <div style="margin-top: 20px;">
                    <form method="POST" action="" style="display: inline;">
                        <?php echo csrfField(); ?>
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?php echo $viewMessage['id']; ?>">
                        <select name="status" style="padding: 8px; border-radius: 5px; border: 2px solid var(--gray-200);">
                            <option value="unread" <?php echo $viewMessage['status'] == 'unread' ? 'selected' : ''; ?>>Unread</option>
                            <option value="read" <?php echo $viewMessage['status'] == 'read' ? 'selected' : ''; ?>>Read</option>
                            <option value="replied" <?php echo $viewMessage['status'] == 'replied' ? 'selected' : ''; ?>>Replied</option>
                        </select>
                        <button type="submit" class="btn btn-donate" style="padding: 8px 15px;">Update Status</button>
                    </form>
                    <a href="mailto:<?php echo htmlspecialchars($viewMessage['email']); ?>" class="btn btn-secondary" style="margin-left: 10px;">
                        <i class="fas fa-reply"></i> Reply via Email
                    </a>
                    <a href="messages.php" class="btn btn-secondary" style="margin-left: 10px;">Close</a>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Messages Table -->
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>From</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $msg): ?>
                        <tr style="<?php echo $msg['status'] == 'unread' ? 'background: var(--light-gold);' : ''; ?>">
                            <td>
                                <strong><?php echo htmlspecialchars($msg['name']); ?></strong>
                                <?php if ($msg['status'] == 'unread'): ?>
                                <span style="color: var(--accent-gold); margin-left: 5px;">●</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($msg['email']); ?></td>
                            <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                            <td><?php echo htmlspecialchars(substr($msg['message'], 0, 50)); ?>...</td>
                            <td>
                                <span class="badge badge-<?php echo $msg['status']; ?>"><?php echo ucfirst($msg['status']); ?></span>
                            </td>
                            <td><?php echo formatDate($msg['created_at']); ?></td>
                            <td>
                                <a href="?view=<?php echo $msg['id']; ?>" class="btn-view"><i class="fas fa-eye"></i> View</a>
                                <form method="POST" action="" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this message?');">
                                    <?php echo csrfField(); ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $msg['id']; ?>">
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
