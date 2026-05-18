<?php
/**
 * Zionite Charity - Admin Login Page
 * Admin login authentication using PHP sessions
 */

require_once '../includes/config.php';

// Redirect if already logged in
if (isAdminLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$message = '';
$messageType = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    securityRequireCsrf();
    securityCheckHoneypot();

    if (!securityLoginRateLimitOk()) {
        $message = 'Too many login attempts. Please try again in ' . SECURITY_LOGIN_LOCKOUT_MINUTES . ' minutes.';
        $messageType = 'error';
    } else {
    $username = cleanInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $message = 'Please enter both username and password.';
        $messageType = 'error';
    } else {
        try {
            // Fetch admin user from database
            $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ? AND status = 'active'");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();
            
            if ($admin && password_verify($password, $admin['password'])) {
                session_regenerate_id(true);
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_name'] = $admin['full_name'];
                $_SESSION['_admin_fingerprint'] = hash('sha256', ($_SERVER['HTTP_USER_AGENT'] ?? '') . securityGetClientIp());
                $_SESSION['_admin_ip'] = securityGetClientIp();
                
                // Update last login
                $updateStmt = $pdo->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
                $updateStmt->execute([$admin['id']]);
                
                // Redirect to dashboard
                header('Location: dashboard.php');
                exit();
            } else {
                securityRecordLoginFailure();
                $message = 'Invalid username or password.';
                $messageType = 'error';
            }
        } catch (PDOException $e) {
            $message = APP_ENV === 'production' ? 'A system error occurred. Please try again later.' : 'Database error: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
    }
}

if (isset($_GET['error']) && $_GET['error'] === 'session') {
    $message = 'Your session expired or was invalid. Please log in again.';
    $messageType = 'error';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Zionite Charity</title>
    
    <link rel="icon" href="<?php echo logoUrl(); ?>" type="image/jpeg">
    
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
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: var(--white);
            padding: 50px;
            border-radius: 15px;
            box-shadow: var(--shadow-xl);
            max-width: 450px;
            width: 100%;
            margin: 20px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .login-header i {
            font-size: 3rem;
            color: var(--accent-gold);
            margin-bottom: 15px;
        }
        
        .login-header h1 {
            color: var(--primary-blue);
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .login-header p {
            color: var(--gray-600);
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--gray-700);
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            font-size: 1rem;
            font-family: inherit;
            transition: var(--transition-fast);
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--accent-gold);
        }
        
        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--accent-gold), var(--light-gold));
            color: var(--primary-blue);
            border: none;
            border-radius: 8px;
            font-size: 1.125rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition-normal);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .back-link {
            text-align: center;
            margin-top: 25px;
        }
        
        .back-link a {
            color: var(--primary-blue);
            font-weight: 600;
        }
        
        .back-link a:hover {
            color: var(--accent-gold);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="<?php echo logoUrl(); ?>" alt="Zionite Charity" class="login-logo">
            <h1>Admin Login</h1>
            <p>Zionite Charity Management System</p>
        </div>
        
        <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>" style="margin-bottom: 25px;">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <?php echo csrfField(); ?>
            <?php echo securityHoneypotField(); ?>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" required autofocus value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
        
        <div class="back-link">
            <a href="<?php echo SITE_URL; ?>/index.php">
                <i class="fas fa-arrow-left"></i> Back to Website
            </a>
        </div>
    </div>
</body>
</html>
