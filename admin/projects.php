<?php
/**
 * Zionite Charity - Admin Projects CRUD
 * Full CRUD operations for projects management
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
        $category = sanitize($_POST['category'] ?? '');
        $location = sanitize($_POST['location'] ?? '');
        $start_date = $_POST['start_date'] ?? '';
        $end_date = $_POST['end_date'] ?? '';
        $budget = floatval($_POST['budget'] ?? 0);
        $status = sanitize($_POST['status'] ?? 'ongoing');
        $featured = isset($_POST['featured']) ? 1 : 0;
        
        // Handle image upload
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $uploadedImage = uploadFile($_FILES['image'], '../uploads/projects');
            if ($uploadedImage) {
                $image = $uploadedImage;
            }
        }
        
        if (empty($title) || empty($description)) {
            $message = 'Please fill in all required fields.';
            $messageType = 'error';
        } else {
            try {
                if ($action === 'add') {
                    $stmt = $pdo->prepare("INSERT INTO projects (title, description, category, image, location, start_date, end_date, budget, status, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$title, $description, $category, $image, $location, $start_date, $end_date, $budget, $status, $featured]);
                    $message = 'Project added successfully!';
                    $messageType = 'success';
                } elseif ($action === 'edit') {
                    $id = intval($_POST['id'] ?? 0);
                    if ($image) {
                        $stmt = $pdo->prepare("UPDATE projects SET title=?, description=?, category=?, image=?, location=?, start_date=?, end_date=?, budget=?, status=?, featured=? WHERE id=?");
                        $stmt->execute([$title, $description, $category, $image, $location, $start_date, $end_date, $budget, $status, $featured, $id]);
                    } else {
                        $stmt = $pdo->prepare("UPDATE projects SET title=?, description=?, category=?, location=?, start_date=?, end_date=?, budget=?, status=?, featured=? WHERE id=?");
                        $stmt->execute([$title, $description, $category, $location, $start_date, $end_date, $budget, $status, $featured, $id]);
                    }
                    $message = 'Project updated successfully!';
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
            // Get project image before deletion
            $stmt = $pdo->prepare("SELECT image FROM projects WHERE id = ?");
            $stmt->execute([$id]);
            $project = $stmt->fetch();
            
            if ($project && $project['image']) {
                deleteFile('../uploads/projects/' . $project['image']);
            }
            
            $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
            $stmt->execute([$id]);
            $message = 'Project deleted successfully!';
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'Database error: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Fetch all projects
try {
    $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
    $projects = $stmt->fetchAll();
} catch (PDOException $e) {
    $projects = [];
}

// Fetch project for editing
$editProject = null;
if (isset($_GET['edit'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([intval($_GET['edit'])]);
        $editProject = $stmt->fetch();
    } catch (PDOException $e) {
        $editProject = null;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects | Zionite Charity Admin</title>
    
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
                    <li><a href="projects.php" class="active"><i class="fas fa-project-diagram"></i> Projects</a></li>
                    <li><a href="reports.php"><i class="fas fa-file-alt"></i> Reports</a></li>
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
                <h1>Projects Management</h1>
                <a href="?add=1" class="btn-add"><i class="fas fa-plus"></i> Add New Project</a>
            </div>
            
            <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>" style="margin-bottom: 25px;">
                <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['add']) || $editProject): ?>
            <!-- Add/Edit Form -->
            <div class="form-modal">
                <h2><?php echo $editProject ? 'Edit Project' : 'Add New Project'; ?></h2>
                <form method="POST" action="" enctype="multipart/form-data">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="action" value="<?php echo $editProject ? 'edit' : 'add'; ?>">
                    <?php if ($editProject): ?>
                    <input type="hidden" name="id" value="<?php echo $editProject['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="title">Project Title *</label>
                        <input type="text" id="title" name="title" class="form-control" required value="<?php echo $editProject ? htmlspecialchars($editProject['title']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description *</label>
                        <textarea id="description" name="description" class="form-control" rows="4" required><?php echo $editProject ? htmlspecialchars($editProject['description']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="category">Category</label>
                            <input type="text" id="category" name="category" class="form-control" value="<?php echo $editProject ? htmlspecialchars($editProject['category']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" id="location" name="location" class="form-control" value="<?php echo $editProject ? htmlspecialchars($editProject['location']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo $editProject ? $editProject['start_date'] : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo $editProject ? $editProject['end_date'] : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="budget">Budget ($)</label>
                            <input type="number" id="budget" name="budget" class="form-control" step="0.01" value="<?php echo $editProject ? $editProject['budget'] : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" class="form-control">
                                <option value="ongoing" <?php echo ($editProject && $editProject['status'] == 'ongoing') ? 'selected' : ''; ?>>Ongoing</option>
                                <option value="completed" <?php echo ($editProject && $editProject['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                <option value="upcoming" <?php echo ($editProject && $editProject['status'] == 'upcoming') ? 'selected' : ''; ?>>Upcoming</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Project Image</label>
                        <input type="file" id="image" name="image" class="form-control" accept="image/*">
                        <?php if ($editProject && $editProject['image']): ?>
                        <small>Current: <?php echo htmlspecialchars($editProject['image']); ?></small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="featured" value="1" <?php echo ($editProject && $editProject['featured']) ? 'checked' : ''; ?>>
                            Featured Project
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-donate">
                        <i class="fas fa-save"></i> <?php echo $editProject ? 'Update Project' : 'Add Project'; ?>
                    </button>
                    <a href="projects.php" class="btn btn-secondary" style="margin-left: 10px;">Cancel</a>
                </form>
            </div>
            <?php endif; ?>
            
            <!-- Projects Table -->
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Budget</th>
                            <th>Featured</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projects as $project): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($project['title']); ?></strong>
                                <?php if ($project['image']): ?>
                                <br><small><i class="fas fa-image"></i> Has image</small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($project['category']); ?></td>
                            <td><?php echo htmlspecialchars($project['location']); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $project['status']; ?>"><?php echo ucfirst($project['status']); ?></span>
                            </td>
                            <td><?php echo formatCurrency($project['budget']); ?></td>
                            <td><?php echo $project['featured'] ? '<i class="fas fa-star" style="color: var(--accent-gold);"></i>' : '-'; ?></td>
                            <td>
                                <a href="?edit=<?php echo $project['id']; ?>" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                <form method="POST" action="" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this project?');">
                                    <?php echo csrfField(); ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
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
