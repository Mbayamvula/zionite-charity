<?php
/**
 * Zionite Charity - Admin Partners CRUD
 * Full CRUD operations for partners management
 */

require_once '../includes/config.php';

requireAdminLogin();

$message = '';
$messageType = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add' || $action === 'edit') {
        $name = sanitize($_POST['name'] ?? '');
        $website = sanitize($_POST['website'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $partnership_type = sanitize($_POST['partnership_type'] ?? 'collaborator');
        $contact_person = sanitize($_POST['contact_person'] ?? '');
        $contact_email = sanitize($_POST['contact_email'] ?? '');
        $status = sanitize($_POST['status'] ?? 'active');
        
        // Handle logo upload
        $logo = '';
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
            $uploadedLogo = uploadFile($_FILES['logo'], '../uploads/projects');
            if ($uploadedLogo) {
                $logo = $uploadedLogo;
            }
        }
        
        if (empty($name)) {
            $message = 'Please fill in all required fields.';
            $messageType = 'error';
        } else {
            try {
                if ($action === 'add') {
                    $stmt = $pdo->prepare("INSERT INTO partners (name, logo, website, description, partnership_type, contact_person, contact_email, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$name, $logo, $website, $description, $partnership_type, $contact_person, $contact_email, $status]);
                    $message = 'Partner added successfully!';
                    $messageType = 'success';
                } elseif ($action === 'edit') {
                    $id = intval($_POST['id'] ?? 0);
                    if ($logo) {
                        $stmt = $pdo->prepare("UPDATE partners SET name=?, logo=?, website=?, description=?, partnership_type=?, contact_person=?, contact_email=?, status=? WHERE id=?");
                        $stmt->execute([$name, $logo, $website, $description, $partnership_type, $contact_person, $contact_email, $status, $id]);
                    } else {
                        $stmt = $pdo->prepare("UPDATE partners SET name=?, website=?, description=?, partnership_type=?, contact_person=?, contact_email=?, status=? WHERE id=?");
                        $stmt->execute([$name, $website, $description, $partnership_type, $contact_person, $contact_email, $status, $id]);
                    }
                    $message = 'Partner updated successfully!';
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
            // Get partner logo before deletion
            $stmt = $pdo->prepare("SELECT logo FROM partners WHERE id = ?");
            $stmt->execute([$id]);
            $partner = $stmt->fetch();
            
            if ($partner && $partner['logo']) {
                deleteFile('../uploads/projects/' . $partner['logo']);
            }
            
            $stmt = $pdo->prepare("DELETE FROM partners WHERE id = ?");
            $stmt->execute([$id]);
            $message = 'Partner deleted successfully!';
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'Database error: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Fetch all partners
try {
    $stmt = $pdo->query("SELECT * FROM partners ORDER BY created_at DESC");
    $partners = $stmt->fetchAll();
} catch (PDOException $e) {
    $partners = [];
}

// Fetch partner for editing
$editPartner = null;
if (isset($_GET['edit'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM partners WHERE id = ?");
        $stmt->execute([intval($_GET['edit'])]);
        $editPartner = $stmt->fetch();
    } catch (PDOException $e) {
        $editPartner = null;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partners | Zionite Charity Admin</title>
    
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
                    <li><a href="reports.php"><i class="fas fa-file-alt"></i> Reports</a></li>
                    <li><a href="volunteers.php"><i class="fas fa-users"></i> Volunteers</a></li>
                    <li><a href="donations.php"><i class="fas fa-donate"></i> Donations</a></li>
                    <li><a href="partners.php" class="active"><i class="fas fa-handshake"></i> Partners</a></li>
                    <li><a href="messages.php"><i class="fas fa-envelope"></i> Messages</a></li>
                    <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Website</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-content">
            <div class="admin-header">
                <h1>Partners Management</h1>
                <a href="?add=1" class="btn-add"><i class="fas fa-plus"></i> Add New Partner</a>
            </div>
            
            <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>" style="margin-bottom: 25px;">
                <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['add']) || $editPartner): ?>
            <!-- Add/Edit Form -->
            <div class="form-modal">
                <h2><?php echo $editPartner ? 'Edit Partner' : 'Add New Partner'; ?></h2>
                <form method="POST" action="" enctype="multipart/form-data">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="action" value="<?php echo $editPartner ? 'edit' : 'add'; ?>">
                    <?php if ($editPartner): ?>
                    <input type="hidden" name="id" value="<?php echo $editPartner['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="name">Partner Name *</label>
                        <input type="text" id="name" name="name" class="form-control" required value="<?php echo $editPartner ? htmlspecialchars($editPartner['name']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="website">Website</label>
                        <input type="url" id="website" name="website" class="form-control" value="<?php echo $editPartner ? htmlspecialchars($editPartner['website']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="3"><?php echo $editPartner ? htmlspecialchars($editPartner['description']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="partnership_type">Partnership Type</label>
                        <select id="partnership_type" name="partnership_type" class="form-control">
                            <option value="collaborator" <?php echo ($editPartner && $editPartner['partnership_type'] == 'collaborator') ? 'selected' : ''; ?>>Collaborator</option>
                            <option value="sponsor" <?php echo ($editPartner && $editPartner['partnership_type'] == 'sponsor') ? 'selected' : ''; ?>>Sponsor</option>
                            <option value="donor" <?php echo ($editPartner && $editPartner['partnership_type'] == 'donor') ? 'selected' : ''; ?>>Donor</option>
                            <option value="media" <?php echo ($editPartner && $editPartner['partnership_type'] == 'media') ? 'selected' : ''; ?>>Media</option>
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="contact_person">Contact Person</label>
                            <input type="text" id="contact_person" name="contact_person" class="form-control" value="<?php echo $editPartner ? htmlspecialchars($editPartner['contact_person']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="contact_email">Contact Email</label>
                            <input type="email" id="contact_email" name="contact_email" class="form-control" value="<?php echo $editPartner ? htmlspecialchars($editPartner['contact_email']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="logo">Partner Logo</label>
                        <input type="file" id="logo" name="logo" class="form-control" accept="image/*">
                        <?php if ($editPartner && $editPartner['logo']): ?>
                        <small>Current: <?php echo htmlspecialchars($editPartner['logo']); ?></small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="active" <?php echo ($editPartner && $editPartner['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo ($editPartner && $editPartner['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-donate">
                        <i class="fas fa-save"></i> <?php echo $editPartner ? 'Update Partner' : 'Add Partner'; ?>
                    </button>
                    <a href="partners.php" class="btn btn-secondary" style="margin-left: 10px;">Cancel</a>
                </form>
            </div>
            <?php endif; ?>
            
            <!-- Partners Table -->
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Website</th>
                            <th>Type</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($partners as $partner): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($partner['name']); ?></strong>
                                <?php if ($partner['logo']): ?>
                                <br><small><i class="fas fa-image"></i> Has logo</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($partner['website']): ?>
                                <a href="<?php echo htmlspecialchars($partner['website']); ?>" target="_blank" style="color: var(--primary-blue);">
                                    <?php echo htmlspecialchars($partner['website']); ?>
                                </a>
                                <?php else: ?>
                                -
                                <?php endif; ?>
                            </td>
                            <td><?php echo ucfirst($partner['partnership_type']); ?></td>
                            <td>
                                <?php if ($partner['contact_person']): ?>
                                <?php echo htmlspecialchars($partner['contact_person']); ?>
                                <?php if ($partner['contact_email']): ?>
                                <br><small><?php echo htmlspecialchars($partner['contact_email']); ?></small>
                                <?php endif; ?>
                                <?php else: ?>
                                -
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-<?php echo $partner['status']; ?>"><?php echo ucfirst($partner['status']); ?></span>
                            </td>
                            <td>
                                <a href="?edit=<?php echo $partner['id']; ?>" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                <form method="POST" action="" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this partner?');">
                                    <?php echo csrfField(); ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $partner['id']; ?>">
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
