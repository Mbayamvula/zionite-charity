<?php
/**
 * Zionite Charity - Admin Dashboard
 * Main admin dashboard with statistics and navigation
 */

require_once '../includes/config.php';

// Require admin login
requireAdminLogin();

// Fetch statistics
try {
    $stats = [];
    
    // Total volunteers
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM volunteers");
    $stats['volunteers'] = $stmt->fetch()['count'];
    
    // Total donations
    $stmt = $pdo->query("SELECT COUNT(*) as count, SUM(amount) as total FROM donations WHERE status = 'completed'");
    $donationStats = $stmt->fetch();
    $stats['donations'] = $donationStats['count'];
    $stats['donation_total'] = $donationStats['total'] ?? 0;
    
    // Total projects
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM projects");
    $stats['projects'] = $stmt->fetch()['count'];
    
    // Ongoing projects
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM projects WHERE status = 'ongoing'");
    $stats['ongoing_projects'] = $stmt->fetch()['count'];
    
    // Pending volunteers
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM volunteers WHERE status = 'pending'");
    $stats['pending_volunteers'] = $stmt->fetch()['count'];
    
    // Pending donations
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM donations WHERE status = 'pending'");
    $stats['pending_donations'] = $stmt->fetch()['count'];
    
    // Unread messages
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM contact_messages WHERE status = 'unread'");
    $stats['unread_messages'] = $stmt->fetch()['count'];
    
    // Total partners
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM partners WHERE status = 'active'");
    $stats['partners'] = $stmt->fetch()['count'];
    
} catch (PDOException $e) {
    $stats = [
        'volunteers' => 0,
        'donations' => 0,
        'donation_total' => 0,
        'projects' => 0,
        'ongoing_projects' => 0,
        'pending_volunteers' => 0,
        'pending_donations' => 0,
        'unread_messages' => 0,
        'partners' => 0
    ];
}

// Fetch recent activities
try {
    $recentVolunteers = $pdo->query("SELECT * FROM volunteers ORDER BY created_at DESC LIMIT 5")->fetchAll();
    $recentDonations = $pdo->query("SELECT * FROM donations ORDER BY created_at DESC LIMIT 5")->fetchAll();
    $recentMessages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5")->fetchAll();
} catch (PDOException $e) {
    $recentVolunteers = [];
    $recentDonations = [];
    $recentMessages = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Zionite Charity Admin</title>
    
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
        
        .admin-header h1 {
            margin: 0;
            color: var(--primary-blue);
        }
        
        .admin-user {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .admin-user-avatar {
            width: 40px;
            height: 40px;
            background: var(--accent-gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-blue);
            font-weight: 700;
        }
        
        .btn-logout {
            background: var(--gray-500);
            color: var(--white);
            padding: 8px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.875rem;
            transition: var(--transition-fast);
        }
        
        .btn-logout:hover {
            background: var(--gray-600);
        }
        
        .recent-section {
            background: var(--white);
            padding: 25px;
            border-radius: 10px;
            box-shadow: var(--shadow-md);
            margin-bottom: 30px;
        }
        
        .recent-section h3 {
            margin-bottom: 20px;
            color: var(--primary-blue);
        }
        
        .recent-item {
            padding: 15px;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .recent-item:last-child {
            border-bottom: none;
        }
        
        .recent-item-info h4 {
            margin: 0 0 5px 0;
            font-size: 1rem;
            color: var(--gray-700);
        }
        
        .recent-item-info p {
            margin: 0;
            font-size: 0.875rem;
            color: var(--gray-500);
        }
        
        .recent-item-date {
            font-size: 0.875rem;
            color: var(--gray-500);
        }
        
        .badge {
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-pending {
            background: var(--light-gold);
            color: var(--primary-blue);
        }
        
        .badge-completed {
            background: #48bb78;
            color: var(--white);
        }
        
        .badge-ongoing {
            background: #4299e1;
            color: var(--white);
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
                    <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="projects.php"><i class="fas fa-project-diagram"></i> Projects</a></li>
                    <li><a href="reports.php"><i class="fas fa-file-alt"></i> Reports</a></li>
                    <li><a href="volunteers.php"><i class="fas fa-users"></i> Volunteers</a></li>
                    <li><a href="donations.php"><i class="fas fa-donate"></i> Donations</a></li>
                    <li><a href="partners.php"><i class="fas fa-handshake"></i> Partners</a></li>
                    <li><a href="messages.php"><i class="fas fa-envelope"></i> Messages <?php if ($stats['unread_messages'] > 0): ?><span class="badge badge-pending"><?php echo $stats['unread_messages']; ?></span><?php endif; ?></a></li>
                    <li><a href="security.php"><i class="fas fa-robot"></i> AI Security</a></li>
                    <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Website</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-content">
            <div class="admin-header">
                <h1>Dashboard</h1>
                <div class="admin-user">
                    <div class="admin-user-avatar">
                        <?php echo strtoupper(substr($_SESSION['admin_name'], 0, 1)); ?>
                    </div>
                    <div>
                        <strong><?php echo htmlspecialchars($_SESSION['admin_name']); ?></strong>
                        <br><small style="color: var(--gray-500);">Administrator</small>
                    </div>
                    <a href="logout.php" class="btn-logout">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
            
            <!-- Statistics -->
            <div class="admin-stats">
                <div class="stat-card">
                    <h3><?php echo number_format($stats['volunteers']); ?></h3>
                    <p>Total Volunteers</p>
                    <?php if ($stats['pending_volunteers'] > 0): ?>
                    <small style="color: var(--accent-gold);"><?php echo $stats['pending_volunteers']; ?> pending</small>
                    <?php endif; ?>
                </div>
                
                <div class="stat-card">
                    <h3><?php echo number_format($stats['donations']); ?></h3>
                    <p>Total Donations</p>
                    <small><?php echo formatCurrency($stats['donation_total']); ?> raised</small>
                </div>
                
                <div class="stat-card">
                    <h3><?php echo number_format($stats['projects']); ?></h3>
                    <p>Total Projects</p>
                    <?php if ($stats['ongoing_projects'] > 0): ?>
                    <small style="color: #4299e1;"><?php echo $stats['ongoing_projects']; ?> ongoing</small>
                    <?php endif; ?>
                </div>
                
                <div class="stat-card">
                    <h3><?php echo number_format($stats['partners']); ?></h3>
                    <p>Active Partners</p>
                </div>
                
                <div class="stat-card">
                    <h3><?php echo number_format($stats['unread_messages']); ?></h3>
                    <p>Unread Messages</p>
                </div>
                
                <div class="stat-card">
                    <h3><?php echo formatCurrency($stats['donation_total']); ?></h3>
                    <p>Total Raised</p>
                </div>
            </div>
            
            <!-- Recent Volunteers -->
            <div class="recent-section">
                <h3><i class="fas fa-users"></i> Recent Volunteer Applications</h3>
                <?php if (!empty($recentVolunteers)): ?>
                    <?php foreach ($recentVolunteers as $volunteer): ?>
                    <div class="recent-item">
                        <div class="recent-item-info">
                            <h4><?php echo htmlspecialchars($volunteer['first_name'] . ' ' . $volunteer['last_name']); ?></h4>
                            <p><?php echo htmlspecialchars($volunteer['email']); ?></p>
                        </div>
                        <div style="text-align: right;">
                            <span class="badge badge-<?php echo $volunteer['status']; ?>"><?php echo ucfirst($volunteer['status']); ?></span>
                            <div class="recent-item-date"><?php echo formatDate($volunteer['created_at']); ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: var(--gray-500);">No volunteer applications yet.</p>
                <?php endif; ?>
                <div style="margin-top: 20px;">
                    <a href="volunteers.php" class="btn btn-secondary">View All Volunteers</a>
                </div>
            </div>
            
            <!-- Recent Donations -->
            <div class="recent-section">
                <h3><i class="fas fa-donate"></i> Recent Donations</h3>
                <?php if (!empty($recentDonations)): ?>
                    <?php foreach ($recentDonations as $donation): ?>
                    <div class="recent-item">
                        <div class="recent-item-info">
                            <h4><?php echo htmlspecialchars($donation['donor_name']); ?></h4>
                            <p><?php echo formatCurrency($donation['amount']); ?> - <?php echo ucfirst($donation['donation_type']); ?></p>
                        </div>
                        <div style="text-align: right;">
                            <span class="badge badge-<?php echo $donation['status']; ?>"><?php echo ucfirst($donation['status']); ?></span>
                            <div class="recent-item-date"><?php echo formatDate($donation['created_at']); ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: var(--gray-500);">No donations yet.</p>
                <?php endif; ?>
                <div style="margin-top: 20px;">
                    <a href="donations.php" class="btn btn-secondary">View All Donations</a>
                </div>
            </div>
            
            <!-- Recent Messages -->
            <div class="recent-section">
                <h3><i class="fas fa-envelope"></i> Recent Contact Messages</h3>
                <?php if (!empty($recentMessages)): ?>
                    <?php foreach ($recentMessages as $message): ?>
                    <div class="recent-item">
                        <div class="recent-item-info">
                            <h4><?php echo htmlspecialchars($message['name']); ?></h4>
                            <p><?php echo htmlspecialchars(substr($message['message'], 0, 50)) . '...'; ?></p>
                        </div>
                        <div style="text-align: right;">
                            <span class="badge badge-<?php echo $message['status']; ?>"><?php echo ucfirst($message['status']); ?></span>
                            <div class="recent-item-date"><?php echo formatDate($message['created_at']); ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: var(--gray-500);">No messages yet.</p>
                <?php endif; ?>
                <div style="margin-top: 20px;">
                    <a href="messages.php" class="btn btn-secondary">View All Messages</a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
