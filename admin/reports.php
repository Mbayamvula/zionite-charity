<?php
/**
 * Zionite Charity - Admin Reports CRUD
 * Full CRUD operations for reports management
 */

require_once '../includes/config.php';

requireAdminLogin();

$message = '';
$messageType = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add' || $action === 'edit') {
        $title = sanitize($_POST['title'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $report_type = sanitize($_POST['report_type'] ?? 'annual');
        $year = intval($_POST['year'] ?? date('Y'));
        $quarter = intval($_POST['quarter'] ?? 0);
        $published_date = $_POST['published_date'] ?? '';
        $status = sanitize($_POST['status'] ?? 'published');
        
        // Handle file upload
        $file_path = '';
        $file_size = 0;
        if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
            $uploadedFile = uploadFile($_FILES['file'], '../uploads/reports');
            if ($uploadedFile) {
                $file_path = $uploadedFile;
                $file_size = $_FILES['file']['size'] / 1024 / 1024; // Convert to MB
            }
        }
        
        if (empty($title)) {
            $message = 'Please fill in all required fields.';
            $messageType = 'error';
        } else {
            try {
                if ($action === 'add') {
                    $stmt = $pdo->prepare("INSERT INTO reports (title, description, report_type, year, quarter, file_path, file_size, published_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$title, $description, $report_type, $year, $quarter, $file_path, $file_size, $published_date, $status]);
                    $message = 'Report added successfully!';
                    $messageType = 'success';
                } elseif ($action === 'edit') {
                    $id = intval($_POST['id'] ?? 0);
                    if ($file_path) {
                        $stmt = $pdo->prepare("UPDATE reports SET title=?, description=?, report_type=?, year=?, quarter=?, file_path=?, file_size=?, published_date=?, status=? WHERE id=?");
                        $stmt->execute([$title, $description, $report_type, $year, $quarter, $file_path, $file_size, $published_date, $status, $id]);
                    } else {
                        $stmt = $pdo->prepare("UPDATE reports SET title=?, description=?, report_type=?, year=?, quarter=?, published_date=?, status=? WHERE id=?");
                        $stmt->execute([$title, $description, $report_type, $year, $quarter, $published_date, $status, $id]);
                    }
                    $message = 'Report updated successfully!';
                    $messageType = 'success';
                }
            } catch (PDOException $e) {
                $message = 'Database error: ' . $e->getMessage();
                $messageType = 'error';
            }
        }
    } elseif ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        try {
            // Get report file before deletion
            $stmt = $pdo->prepare("SELECT file_path FROM reports WHERE id = ?");
            $stmt->execute([$id]);
            $report = $stmt->fetch();
            
            if ($report && $report['file_path']) {
                deleteFile('../uploads/reports/' . $report['file_path']);
            }
            
            $stmt = $pdo->prepare("DELETE FROM reports WHERE id = ?");
            $stmt->execute([$id]);
            $message = 'Report deleted successfully!';
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'Database error: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Fetch all reports
try {
    $stmt = $pdo->query("SELECT * FROM reports ORDER BY published_date DESC, created_at DESC");
    $reports = $stmt->fetchAll();
} catch (PDOException $e) {
    $reports = [];
}

// Fetch report for editing
$editReport = null;
if (isset($_GET['edit'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM reports WHERE id = ?");
        $stmt->execute([intval($_GET['edit'])]);
        $editReport = $stmt->fetch();
    } catch (PDOException $e) {
        $editReport = null;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports | Zionite Charity Admin</title>
    
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
        
        .btn-add {
            background: var(--accent-gold);
            color: var(--primary-blue);
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
        }
        
        .btn-edit {
            background: #4299e1;
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
        
        .form-modal {
            background: var(--white);
            padding: 30px;
            border-radius: 10px;
            box-shadow: var(--shadow-lg);
            margin-bottom: 30px;
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
                    <li><a href="reports.php" class="active"><i class="fas fa-file-alt"></i> Reports</a></li>
                    <li><a href="volunteers.php"><i class="fas fa-users"></i> Volunteers</a></li>
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
                <h1>Reports Management</h1>
                <a href="?add=1" class="btn-add"><i class="fas fa-plus"></i> Add New Report</a>
            </div>
            
            <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>" style="margin-bottom: 25px;">
                <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['add']) || $editReport): ?>
            <!-- Add/Edit Form -->
            <div class="form-modal">
                <h2><?php echo $editReport ? 'Edit Report' : 'Add New Report'; ?></h2>
                <form method="POST" action="" enctype="multipart/form-data">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="action" value="<?php echo $editReport ? 'edit' : 'add'; ?>">
                    <?php if ($editReport): ?>
                    <input type="hidden" name="id" value="<?php echo $editReport['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="title">Report Title *</label>
                        <input type="text" id="title" name="title" class="form-control" required value="<?php echo $editReport ? htmlspecialchars($editReport['title']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="3"><?php echo $editReport ? htmlspecialchars($editReport['description']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="report_type">Report Type</label>
                            <select id="report_type" name="report_type" class="form-control">
                                <option value="annual" <?php echo ($editReport && $editReport['report_type'] == 'annual') ? 'selected' : ''; ?>>Annual</option>
                                <option value="quarterly" <?php echo ($editReport && $editReport['report_type'] == 'quarterly') ? 'selected' : ''; ?>>Quarterly</option>
                                <option value="project" <?php echo ($editReport && $editReport['report_type'] == 'project') ? 'selected' : ''; ?>>Project</option>
                                <option value="financial" <?php echo ($editReport && $editReport['report_type'] == 'financial') ? 'selected' : ''; ?>>Financial</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="year">Year</label>
                            <input type="number" id="year" name="year" class="form-control" value="<?php echo $editReport ? $editReport['year'] : date('Y'); ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="quarter">Quarter (for quarterly reports)</label>
                            <select id="quarter" name="quarter" class="form-control">
                                <option value="0" <?php echo ($editReport && $editReport['quarter'] == 0) ? 'selected' : ''; ?>>N/A</option>
                                <option value="1" <?php echo ($editReport && $editReport['quarter'] == 1) ? 'selected' : ''; ?>>Q1</option>
                                <option value="2" <?php echo ($editReport && $editReport['quarter'] == 2) ? 'selected' : ''; ?>>Q2</option>
                                <option value="3" <?php echo ($editReport && $editReport['quarter'] == 3) ? 'selected' : ''; ?>>Q3</option>
                                <option value="4" <?php echo ($editReport && $editReport['quarter'] == 4) ? 'selected' : ''; ?>>Q4</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="published_date">Published Date</label>
                            <input type="date" id="published_date" name="published_date" class="form-control" value="<?php echo $editReport ? $editReport['published_date'] : date('Y-m-d'); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="file">Report File (PDF)</label>
                        <input type="file" id="file" name="file" class="form-control" accept=".pdf">
                        <?php if ($editReport && $editReport['file_path']): ?>
                        <small>Current: <?php echo htmlspecialchars($editReport['file_path']); ?> (<?php echo number_format($editReport['file_size'], 2); ?> MB)</small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="published" <?php echo ($editReport && $editReport['status'] == 'published') ? 'selected' : ''; ?>>Published</option>
                            <option value="draft" <?php echo ($editReport && $editReport['status'] == 'draft') ? 'selected' : ''; ?>>Draft</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-donate">
                        <i class="fas fa-save"></i> <?php echo $editReport ? 'Update Report' : 'Add Report'; ?>
                    </button>
                    <a href="reports.php" class="btn btn-secondary" style="margin-left: 10px;">Cancel</a>
                </form>
            </div>
            <?php endif; ?>
            
            <!-- Reports Table -->
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Year/Quarter</th>
                            <th>Published Date</th>
                            <th>File</th>
                            <th>Downloads</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $report): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($report['title']); ?></strong>
                                <?php if ($report['description']): ?>
                                <br><small><?php echo htmlspecialchars(substr($report['description'], 0, 50)); ?>...</small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo ucfirst($report['report_type']); ?></td>
                            <td>
                                <?php echo $report['year']; ?>
                                <?php if ($report['quarter'] > 0): ?>
                                Q<?php echo $report['quarter']; ?>
                                <?php endif; ?>
                            </td>
                            <td><?php echo formatDate($report['published_date']); ?></td>
                            <td>
                                <?php if ($report['file_path']): ?>
                                <a href="<?php echo SITE_URL; ?>/uploads/reports/<?php echo htmlspecialchars($report['file_path']); ?>" target="_blank" style="color: var(--primary-blue);">
                                    <i class="fas fa-file-pdf"></i> View
                                </a>
                                <?php else: ?>
                                <span style="color: var(--gray-500);">No file</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo number_format($report['download_count']); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $report['status']; ?>"><?php echo ucfirst($report['status']); ?></span>
                            </td>
                            <td>
                                <a href="?edit=<?php echo $report['id']; ?>" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                <form method="POST" action="" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this report?');">
                                    <?php echo csrfField(); ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $report['id']; ?>">
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
