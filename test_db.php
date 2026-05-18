<?php
/**
 * Database test — localhost only (local) or maintenance token (production)
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
} elseif (SECURITY_RESTRICT_DEBUG_SCRIPTS) {
    securityRequireLocalOrToken();
}

define('DB_HOST', $env['db_host']);
define('DB_NAME', $env['db_name']);
define('DB_USER', $env['db_user']);
define('DB_PASS', $env['db_pass']);

echo '<h2>Database connection test</h2><hr>';

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
    echo '<p style="color:green;">Database connection successful.</p>';
    $tables = ['admin_users', 'volunteers', 'donations', 'projects', 'security_events'];
    foreach ($tables as $table) {
        $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        echo '<p>' . htmlspecialchars($table) . ': ' . (int) $count . ' rows</p>';
    }
} catch (PDOException $e) {
    echo '<p style="color:red;">Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

echo '<p><a href="index.php">Back to home</a></p>';
