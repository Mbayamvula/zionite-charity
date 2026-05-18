<?php
/**
 * Security bootstrap — headers, session, CSRF, rate limits
 */

require_once __DIR__ . '/security_config.php';
require_once __DIR__ . '/security_agent.php';

function securityInitSession() {
    if (session_status() !== PHP_SESSION_NONE) {
        return;
    }

    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.gc_maxlifetime', (string) SECURITY_SESSION_LIFETIME);

    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', '1');
    }

    session_name('ZCSESSID');
    session_start();

    if (empty($_SESSION['_security_created'])) {
        $_SESSION['_security_created'] = time();
        session_regenerate_id(true);
    }
}

function securitySendHeaders() {
    if (headers_sent()) {
        return;
    }

    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

    if (APP_ENV === 'production') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }

    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://js.stripe.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; img-src 'self' data: https:; frame-src https://js.stripe.com https://www.paypal.com; connect-src 'self' https://api.stripe.com https://api-m.sandbox.paypal.com https://api-m.paypal.com;");
}

function securityBootstrap(PDO $pdo = null) {
    securityInitSession();
    securitySendHeaders();

    if (!securityAgentRun($pdo)) {
        securityRenderBlockPage();
    }
}

function csrfToken() {
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

function csrfField() {
    return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars(csrfToken()) . '">';
}

function verifyCsrfToken() {
    $token = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    return $token !== '' && hash_equals(csrfToken(), $token);
}

function securityRequireCsrf() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !verifyCsrfToken()) {
        $report = [
            'score' => 90,
            'threats' => [['type' => 'csrf_invalid', 'weight' => 90, 'detail' => 'Invalid or missing CSRF token']],
            'ip' => securityGetClientIp(),
            'action' => 'block',
        ];
        securityAgentLog($report);
        securityBlockIp($report['ip'], 'CSRF validation failed', SECURITY_AUTO_BLOCK_DURATION);
        securityRenderBlockPage();
    }
}

function securityCheckHoneypot() {
    if (!empty($_POST[SECURITY_HONEYPOT_FIELD])) {
        securityBlockIp(securityGetClientIp(), 'Honeypot triggered', SECURITY_AUTO_BLOCK_DURATION);
        securityRenderBlockPage();
    }
}

function securityHoneypotField() {
    return '<div style="position:absolute;left:-9999px;top:-9999px;" aria-hidden="true">'
        . '<label for="' . SECURITY_HONEYPOT_FIELD . '">Leave empty</label>'
        . '<input type="text" id="' . SECURITY_HONEYPOT_FIELD . '" name="' . SECURITY_HONEYPOT_FIELD . '" value="" tabindex="-1" autocomplete="off">'
        . '</div>';
}

/**
 * Rate limit by key (e.g. login, contact, donation)
 */
function securityRateLimit($key, $maxAttempts, $windowSeconds) {
    securityEnsureStorage();
    $ip = securityGetClientIp();
    $file = SECURITY_STORAGE_PATH . '/rate_' . md5($key . '_' . $ip) . '.json';
    $now = time();
    $data = ['count' => 0, 'start' => $now];

    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true) ?: $data;
        if (($now - $data['start']) > $windowSeconds) {
            $data = ['count' => 0, 'start' => $now];
        }
    }

    $data['count']++;
    file_put_contents($file, json_encode($data), LOCK_EX);

    return $data['count'] <= $maxAttempts;
}

function securityLoginRateLimitOk() {
    return securityRateLimit(
        'admin_login',
        SECURITY_MAX_LOGIN_ATTEMPTS,
        SECURITY_LOGIN_LOCKOUT_MINUTES * 60
    );
}

function securityRecordLoginFailure() {
    $ip = securityGetClientIp();
    $report = [
        'score' => 55,
        'threats' => [['type' => 'login_failed', 'weight' => 55, 'detail' => 'Failed admin login attempt']],
        'ip' => $ip,
        'action' => 'log',
    ];
    securityAgentLog($report);

    if (!securityLoginRateLimitOk()) {
        securityBlockIp($ip, 'Too many failed login attempts', SECURITY_LOGIN_LOCKOUT_MINUTES * 60);
    }
}

function securityValidateAdminSession() {
    if (!isAdminLoggedIn()) {
        return;
    }
    $ip = securityGetClientIp();
    $fp = hash('sha256', ($_SERVER['HTTP_USER_AGENT'] ?? '') . ($ip ?? ''));

    if (empty($_SESSION['_admin_fingerprint'])) {
        $_SESSION['_admin_fingerprint'] = $fp;
        $_SESSION['_admin_ip'] = $ip;
        return;
    }

    if ($_SESSION['_admin_fingerprint'] !== $fp) {
        session_unset();
        session_destroy();
        header('Location: ' . SITE_URL . '/admin/login.php?error=session');
        exit;
    }
}

function securityIsLocalRequest() {
    $ip = securityGetClientIp();
    return in_array($ip, ['127.0.0.1', '::1'], true);
}

function securityRequireLocalOrToken() {
    $token = $_GET['token'] ?? '';
    if (securityIsLocalRequest()) {
        return;
    }
    if ($token !== '' && hash_equals(SECURITY_MAINTENANCE_TOKEN, $token)) {
        return;
    }
    http_response_code(403);
    die('Access denied. This script is restricted.');
}

function cleanInput($data) {
    return trim(stripslashes($data ?? ''));
}

function e($string) {
    return htmlspecialchars((string) $string, ENT_QUOTES, 'UTF-8');
}
