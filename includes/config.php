<?php
/**
 * Zionite Charity - Main configuration
 * Loads settings from includes/env.php (copy env.example.php for production)
 */

$envFile = __DIR__ . '/env.php';
if (!is_file($envFile)) {
    http_response_code(503);
    die('Configuration missing. Copy includes/env.example.php to includes/env.php and configure your settings.');
}
$env = require $envFile;

// Application
define('APP_ENV', $env['app_env'] ?? 'local');

// Database
define('DB_HOST', $env['db_host'] ?? 'localhost');
define('DB_NAME', $env['db_name'] ?? 'zionite_charity');
define('DB_USER', $env['db_user'] ?? 'root');
define('DB_PASS', $env['db_pass'] ?? '');
define('DB_CHARSET', $env['db_charset'] ?? 'utf8mb4');

// Site
define('SITE_NAME', $env['site_name'] ?? 'Zionite Charity');
define('SITE_URL', rtrim($env['site_url'] ?? '', '/'));
define('ADMIN_EMAIL', $env['admin_email'] ?? 'admin@zionitecharity.org');

// Payments
define('STRIPE_SECRET_KEY', $env['stripe_secret_key'] ?? '');
define('STRIPE_PUBLISHABLE_KEY', $env['stripe_publishable_key'] ?? '');
define('PAYPAL_CLIENT_ID', $env['paypal_client_id'] ?? '');
define('PAYPAL_CLIENT_SECRET', $env['paypal_client_secret'] ?? '');
define('PAYPAL_MODE', $env['paypal_mode'] ?? 'sandbox');
define('BANK_NAME', $env['bank_name'] ?? '');
define('BANK_ACCOUNT_NAME', $env['bank_account_name'] ?? '');
define('BANK_ACCOUNT_NUMBER', $env['bank_account_number'] ?? '');
define('BANK_ROUTING', $env['bank_routing'] ?? '');
define('BANK_IBAN', $env['bank_iban'] ?? '');
define('BANK_SWIFT', $env['bank_swift'] ?? '');
define('CASH_INSTRUCTIONS', $env['cash_instructions'] ?? '');
define('DONATION_CURRENCY', $env['donation_currency'] ?? 'usd');
define('DONATION_CURRENCY_SYMBOL', $env['donation_currency_symbol'] ?? '$');

define('SECURITY_MAINTENANCE_TOKEN', $env['security_maintenance_token'] ?? 'change-me');

require_once __DIR__ . '/security_config.php';

// Error reporting
if (APP_ENV === 'production') {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

require_once __DIR__ . '/security.php';

// English translations
$translations = require __DIR__ . '/../languages/en.php';

function t($key) {
    global $translations;
    return $translations[$key] ?? $key;
}

require_once __DIR__ . '/project_helpers.php';
require_once __DIR__ . '/payment_helpers.php';

function getDBConnection() {
    try {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => false,
        ];
        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        if (APP_ENV === 'production') {
            error_log('Database connection failed: ' . $e->getMessage());
            die('The site is temporarily unavailable. Please try again later.');
        }
        die('Database Connection Failed: ' . $e->getMessage());
    }
}

function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function formatDate($date, $format = 'F j, Y') {
    return date($format, strtotime($date));
}

function formatCurrency($amount) {
    return DONATION_CURRENCY_SYMBOL . number_format((float) $amount, 2);
}

function uploadFile($file, $directory) {
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
    $fileName = $file['name'];
    $fileTmp = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];

    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($fileExt, $allowedTypes, true)) {
        return false;
    }
    if ($fileSize > 5242880 || $fileError !== 0) {
        return false;
    }

    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }

    $newFileName = uniqid('', true) . '.' . $fileExt;
    $destination = $directory . '/' . $newFileName;

    return move_uploaded_file($fileTmp, $destination) ? $newFileName : false;
}

function deleteFile($filePath) {
    return file_exists($filePath) && unlink($filePath);
}

function ensureUploadDirectories() {
    $dirs = [
        dirname(__DIR__) . '/uploads/projects',
        dirname(__DIR__) . '/uploads/reports',
        dirname(__DIR__) . '/storage/security',
    ];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}

ensureUploadDirectories();

$pdo = getDBConnection();
securityBootstrap($pdo);

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: ' . SITE_URL . '/admin/login.php');
        exit();
    }
    securityValidateAdminSession();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        securityRequireCsrf();
    }
}

function isProduction() {
    return APP_ENV === 'production';
}

function logoUrl() {
    return SITE_URL . '/assets/images/logo%20zionite%20charity.jpg';
}
