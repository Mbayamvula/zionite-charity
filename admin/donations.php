<?php
/**
 * Zionite Charity - Admin Donations CRUD
 * Full CRUD operations for donations management
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
            $stmt = $pdo->prepare("UPDATE donations SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
            $message = 'Donation status updated successfully!';
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'Database error: ' . $e->getMessage();
            $messageType = 'error';
        }
    } elseif ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        try {
            $stmt = $pdo->prepare("DELETE FROM donations WHERE id = ?");
            $stmt->execute([$id]);
            $message = 'Donation deleted successfully!';
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'Database error: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Fetch all donations
try {
    $stmt = $pdo->query("SELECT * FROM donations ORDER BY created_at DESC");
    $donations = $stmt->fetchAll();
} catch (PDOException $e) {
    $donations = [];
}

// Fetch donation for viewing details
$viewDonation = null;
if (isset($_GET['view'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM donations WHERE id = ?");
        $stmt->execute([intval($_GET['view'])]);
        $viewDonation = $stmt->fetch();
    } catch (PDOException $e) {
        $viewDonation = null;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donations | Zionite Charity Admin</title>
    
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
                    <li><a href="volunteers.php"><i class="fas fa-users"></i> Volunteers</a></li>
                    <li><a href="donations.php" class="active"><i class="fas fa-donate"></i> Donations</a></li>
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
                <h1>Donations Management</h1>
            </div>
            
            <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>" style="margin-bottom: 25px;">
                <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>
            
            <?php if ($viewDonation): ?>
            <!-- Donation Details Modal -->
            <div class="detail-modal">
                <h2>Donation Details</h2>
                <div class="detail-row">
                    <div class="detail-label">Donor Name:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($viewDonation['donor_name']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Email:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($viewDonation['email']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Phone:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($viewDonation['phone']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Amount:</div>
                    <div class="detail-value" style="font-size: 1.25rem; font-weight: 700; color: var(--accent-gold);"><?php echo formatCurrency($viewDonation['amount']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Donation Type:</div>
                    <div class="detail-value"><?php echo ucfirst($viewDonation['donation_type']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Payment Method:</div>
                    <div class="detail-value"><?php echo ucfirst(str_replace('-', ' ', $viewDonation['payment_method'])); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Purpose:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($viewDonation['purpose']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Anonymous:</div>
                    <div class="detail-value"><?php echo $viewDonation['is_anonymous'] ? 'Yes' : 'No'; ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Message:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($viewDonation['message']); ?></div>
                </div>
                <?php if (!empty($viewDonation['payment_reference'])): ?>
                <div class="detail-row">
                    <div class="detail-label">Payment Reference:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($viewDonation['payment_reference']); ?></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($viewDonation['transaction_id'])): ?>
                <div class="detail-row">
                    <div class="detail-label">Transaction ID:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($viewDonation['transaction_id']); ?></div>
                </div>
                <?php endif; ?>
                <div class="detail-row">
                    <div class="detail-label">Status:</div>
                    <div class="detail-value">
                        <span class="badge badge-<?php echo $viewDonation['status']; ?>"><?php echo ucfirst($viewDonation['status']); ?></span>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Donated On:</div>
                    <div class="detail-value"><?php echo formatDate($viewDonation['created_at']); ?></div>
                </div>
                
                <div style="margin-top: 20px;">
                    <form method="POST" action="" style="display: inline;">
                        <?php echo csrfField(); ?>
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?php echo $viewDonation['id']; ?>">
                        <select name="status" style="padding: 8px; border-radius: 5px; border: 2px solid var(--gray-200);">
                            <option value="pending" <?php echo $viewDonation['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="completed" <?php echo $viewDonation['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="failed" <?php echo $viewDonation['status'] == 'failed' ? 'selected' : ''; ?>>Failed</option>
                        </select>
                        <button type="submit" class="btn btn-donate" style="padding: 8px 15px;">Update Status</button>
                    </form>
                    <a href="donations.php" class="btn btn-secondary" style="margin-left: 10px;">Close</a>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Donations Table -->
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Donor Name</th>
                            <th>Email</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($donations as $donation): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($donation['donor_name']); ?></strong>
                                <?php if ($donation['is_anonymous']): ?>
                                <br><small><i class="fas fa-user-secret"></i> Anonymous</small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($donation['email']); ?></td>
                            <td style="font-weight: 700; color: var(--accent-gold);"><?php echo formatCurrency($donation['amount']); ?></td>
                            <td><?php echo ucfirst($donation['donation_type']); ?></td>
                            <td><?php echo ucfirst(str_replace('-', ' ', $donation['payment_method'])); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $donation['status']; ?>"><?php echo ucfirst($donation['status']); ?></span>
                            </td>
                            <td><?php echo formatDate($donation['created_at']); ?></td>
                            <td>
                                <a href="?view=<?php echo $donation['id']; ?>" class="btn-view"><i class="fas fa-eye"></i> View</a>
                                <form method="POST" action="" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this donation?');">
                                    <?php echo csrfField(); ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $donation['id']; ?>">
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
