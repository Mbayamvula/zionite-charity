<?php
/**
 * Reset admin password — DISABLED in production unless ?token= is set
 */

$envFile = __DIR__ . '/includes/env.php';
if (!is_file($envFile)) {
    die('Missing includes/env.php');
}
$env = require $envFile;

require_once __DIR__ . '/includes/security_config.php';
require_once __DIR__ . '/includes/security_agent.php';
require_once __DIR__ . '/includes/security.php';
securityInitSession();

if (($env['app_env'] ?? 'local') === 'production') {
    $token = $_GET['token'] ?? '';
    if ($token === '' || !hash_equals($env['security_maintenance_token'] ?? '', $token)) {
        http_response_code(403);
        die('This script is disabled in production.');
    }
} else {
    securityRequireLocalOrToken();
}

define('DB_HOST', $env['db_host']);
define('DB_NAME', $env['db_name']);
define('DB_USER', $env['db_user']);
define('DB_PASS', $env['db_pass']);

echo '<h2>Admin password reset</h2><hr>';

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare('SELECT id FROM admin_users WHERE username = ?');
    $stmt->execute(['admin']);
    $admin = $stmt->fetch();

    $newPassword = bin2hex(random_bytes(4));
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    if ($admin) {
        $pdo->prepare('UPDATE admin_users SET password = ? WHERE username = ?')->execute([$hashedPassword, 'admin']);
        echo '<p style="color:green;">Password reset for user <strong>admin</strong>.</p>';
    } else {
        $pdo->prepare('INSERT INTO admin_users (username, password, email, full_name, status) VALUES (?, ?, ?, ?, ?)')
            ->execute(['admin', $hashedPassword, 'admin@zionitecharity.org', 'Administrator', 'active']);
        echo '<p style="color:green;">Admin user created.</p>';
    }

    echo '<p><strong>Username:</strong> admin<br><strong>New password:</strong> ' . htmlspecialchars($newPassword) . '</p>';
    echo '<p style="color:#c53030;"><strong>Change this password immediately after logging in.</strong></p>';
    echo '<p><a href="admin/login.php">Go to admin login</a></p>';
} catch (PDOException $e) {
    echo '<p style="color:red;">Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
}
