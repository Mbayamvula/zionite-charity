<?php
/**
 * Security agent settings (non-secret)
 * Secrets are in env.php
 */

define('SECURITY_ENABLED', true);
define('SECURITY_BLOCK_THRESHOLD', 70);
define('SECURITY_LOG_THRESHOLD', 35);
define('SECURITY_MAX_LOGIN_ATTEMPTS', 5);
define('SECURITY_LOGIN_LOCKOUT_MINUTES', 20);
define('SECURITY_RATE_LIMIT_WINDOW', 60);
define('SECURITY_MAX_REQUESTS_PER_MINUTE', 100);
define('SECURITY_HONEYPOT_FIELD', 'website_url');
define('SECURITY_RESTRICT_DEBUG_SCRIPTS', true);
define('SECURITY_SESSION_LIFETIME', 7200);
define('SECURITY_AUTO_BLOCK_DURATION', 3600);
define('SECURITY_STORAGE_PATH', dirname(__DIR__) . '/storage/security');
