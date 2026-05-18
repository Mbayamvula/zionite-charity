<?php
/**
 * Zionite AI Security Agent
 * Real-time threat analysis: pattern detection, behavioral scoring, auto-block
 */

require_once __DIR__ . '/security_config.php';

/**
 * @return string
 */
function securityGetClientIp() {
    $keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = $_SERVER[$key];
            if (strpos($ip, ',') !== false) {
                $ip = trim(explode(',', $ip)[0]);
            }
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function securityEnsureStorage() {
    if (!is_dir(SECURITY_STORAGE_PATH)) {
        mkdir(SECURITY_STORAGE_PATH, 0755, true);
    }
}

/**
 * @return array<string, array{until: int, reason: string}>
 */
function securityLoadBlockedIps() {
    securityEnsureStorage();
    $file = SECURITY_STORAGE_PATH . '/blocked_ips.json';
    if (!file_exists($file)) {
        return [];
    }
    $data = json_decode(file_get_contents($file), true);
    return is_array($data) ? $data : [];
}

function securitySaveBlockedIps(array $blocks) {
    securityEnsureStorage();
    file_put_contents(
        SECURITY_STORAGE_PATH . '/blocked_ips.json',
        json_encode($blocks, JSON_PRETTY_PRINT),
        LOCK_EX
    );
}

function securityIsIpBlocked($ip) {
    $blocks = securityLoadBlockedIps();
    if (!isset($blocks[$ip])) {
        return false;
    }
    $until = (int) ($blocks[$ip]['until'] ?? 0);
    if ($until === 0 || $until > time()) {
        return true;
    }
    unset($blocks[$ip]);
    securitySaveBlockedIps($blocks);
    return false;
}

function securityBlockIp($ip, $reason, $duration = null) {
    $blocks = securityLoadBlockedIps();
    $duration = $duration ?? SECURITY_AUTO_BLOCK_DURATION;
    $blocks[$ip] = [
        'until' => $duration > 0 ? time() + $duration : 0,
        'reason' => $reason,
        'blocked_at' => date('Y-m-d H:i:s'),
    ];
    securitySaveBlockedIps($blocks);
}

function securityUnblockIp($ip) {
    $blocks = securityLoadBlockedIps();
    unset($blocks[$ip]);
    securitySaveBlockedIps($blocks);
}

/**
 * AI-style threat patterns with severity weights
 * @return array<int, array{type: string, weight: int, detail: string}>
 */
function securityAnalyzePayload($text) {
    $findings = [];
    $text = strtolower($text);

    $patterns = [
        ['sql_injection', 45, '/(\bunion\b.+\bselect\b|\bselect\b.+\bfrom\b|\bdrop\b\s+\b(table|database)\b|\bor\s+1\s*=\s*1\b|\bor\s+\'1\'\s*=\s*\'1\'|;\s*--|\'\s*or\s*\'|benchmark\s*\(|sleep\s*\(\s*\d+)/i'],
        ['xss', 40, '/(<script|javascript\s*:|onerror\s*=|onload\s*=|<iframe|<svg\s+onload)/i'],
        ['path_traversal', 50, '/(\.\.\/|\.\.\\\\|\/etc\/passwd|\/proc\/self)/i'],
        ['command_injection', 55, '/(\||;)\s*(cat|ls|wget|curl|bash|sh|cmd|powershell)\b/i'],
        ['file_inclusion', 50, '/(php:\/\/|data:text\/html|expect:\/\/)/i'],
        ['scanner_probe', 35, '/(wp-admin|wp-login|\.env|phpmyadmin|\/admin\/|sqlmap|nikto|acunetix|\.git\/)/i'],
        ['ldap_injection', 40, '/(\*\)|\(&|\(objectclass)/i'],
    ];

    foreach ($patterns as [$type, $weight, $regex]) {
        if (preg_match($regex, $text)) {
            $findings[] = [
                'type' => $type,
                'weight' => $weight,
                'detail' => 'Pattern match: ' . $type,
            ];
        }
    }

    return $findings;
}

/**
 * Behavioral analysis — request frequency
 */
function securityAnalyzeBehavior($ip) {
    securityEnsureStorage();
    $findings = [];
    $file = SECURITY_STORAGE_PATH . '/behavior_' . md5($ip) . '.json';
    $now = time();
    $window = 10;
    $maxBurst = 25;

    $hits = [];
    if (file_exists($file)) {
        $hits = json_decode(file_get_contents($file), true) ?: [];
    }
    $hits = array_filter($hits, fn($t) => ($now - $t) < $window);
    $hits[] = $now;
    file_put_contents($file, json_encode(array_values($hits)), LOCK_EX);

    if (count($hits) > $maxBurst) {
        $findings[] = [
            'type' => 'rate_burst',
            'weight' => 50,
            'detail' => count($hits) . ' requests in ' . $window . 's (bot/scanner suspected)',
        ];
    }

    return $findings;
}

/**
 * Suspicious user agents
 */
function securityAnalyzeUserAgent() {
    $ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
    $findings = [];

    if ($ua === '' || strlen($ua) < 10) {
        $findings[] = ['type' => 'empty_ua', 'weight' => 15, 'detail' => 'Missing or short User-Agent'];
    }

    $badBots = ['sqlmap', 'nikto', 'masscan', 'nmap', 'dirbuster', 'gobuster', 'havij', 'libwww-perl'];
    foreach ($badBots as $bot) {
        if (strpos($ua, $bot) !== false) {
            $findings[] = ['type' => 'malicious_bot', 'weight' => 60, 'detail' => 'Known attack tool: ' . $bot];
            break;
        }
    }

    return $findings;
}

/**
 * Main agent analysis — returns threat report
 * @return array{score: int, threats: array, ip: string, action: string}
 */
function securityAgentAnalyze() {
    $ip = securityGetClientIp();
    $score = 0;
    $threats = [];

    if (securityIsIpBlocked($ip)) {
        return [
            'score' => 100,
            'threats' => [['type' => 'blocked_ip', 'weight' => 100, 'detail' => 'IP previously blocked by AI agent']],
            'ip' => $ip,
            'action' => 'block',
        ];
    }

    $payloads = [];
    $payloads[] = $_SERVER['REQUEST_URI'] ?? '';
    $payloads[] = $_SERVER['QUERY_STRING'] ?? '';
    foreach ($_GET as $v) {
        $payloads[] = is_string($v) ? $v : json_encode($v);
    }
    foreach ($_POST as $k => $v) {
        if ($k === SECURITY_HONEYPOT_FIELD) {
            continue;
        }
        $payloads[] = is_string($v) ? $v : json_encode($v);
    }
    $raw = file_get_contents('php://input');
    if ($raw) {
        $payloads[] = substr($raw, 0, 2000);
    }

    foreach ($payloads as $p) {
        foreach (securityAnalyzePayload($p) as $f) {
            $threats[] = $f;
            $score += $f['weight'];
        }
    }

    foreach (securityAnalyzeBehavior($ip) as $f) {
        $threats[] = $f;
        $score += $f['weight'];
    }

    foreach (securityAnalyzeUserAgent() as $f) {
        $threats[] = $f;
        $score += $f['weight'];
    }

    // Honeypot filled = bot
    if (!empty($_POST[SECURITY_HONEYPOT_FIELD])) {
        $threats[] = ['type' => 'honeypot', 'weight' => 80, 'detail' => 'Honeypot field filled by bot'];
        $score += 80;
    }

    $score = min(100, $score);

    $action = 'allow';
    if ($score >= SECURITY_BLOCK_THRESHOLD) {
        $action = 'block';
    } elseif ($score >= SECURITY_LOG_THRESHOLD) {
        $action = 'log';
    }

    return [
        'score' => $score,
        'threats' => $threats,
        'ip' => $ip,
        'action' => $action,
    ];
}

function securityAgentLog($report, PDO $pdo = null) {
    securityEnsureStorage();
    $line = json_encode([
        'time' => date('c'),
        'ip' => $report['ip'],
        'score' => $report['score'],
        'action' => $report['action'],
        'uri' => $_SERVER['REQUEST_URI'] ?? '',
        'method' => $_SERVER['REQUEST_METHOD'] ?? '',
        'threats' => $report['threats'],
        'ua' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 200),
    ]) . PHP_EOL;

    file_put_contents(SECURITY_STORAGE_PATH . '/events.log', $line, FILE_APPEND | LOCK_EX);

    if ($pdo) {
        securityEnsureTables($pdo);
        try {
            $types = array_unique(array_column($report['threats'], 'type'));
            $stmt = $pdo->prepare(
                "INSERT INTO security_events (ip_address, request_uri, request_method, threat_score, threat_types, action_taken, user_agent, details)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $report['ip'],
                substr($_SERVER['REQUEST_URI'] ?? '', 0, 500),
                $_SERVER['REQUEST_METHOD'] ?? 'GET',
                $report['score'],
                implode(',', $types),
                $report['action'],
                substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
                json_encode($report['threats']),
            ]);
        } catch (PDOException $e) {
            // File log is sufficient
        }
    }
}

function securityEnsureTables(PDO $pdo) {
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS security_events (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ip_address VARCHAR(45) NOT NULL,
            request_uri VARCHAR(500),
            request_method VARCHAR(10),
            threat_score INT DEFAULT 0,
            threat_types VARCHAR(255),
            action_taken VARCHAR(20),
            user_agent VARCHAR(255),
            details TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_ip (ip_address),
            INDEX idx_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    } catch (PDOException $e) {
        // ignore
    }
}

/**
 * Run agent before page load; returns false if request must be blocked
 */
function securityAgentRun(PDO $pdo = null) {
    if (!SECURITY_ENABLED) {
        return true;
    }

    $report = securityAgentAnalyze();

    if ($report['action'] === 'log' || $report['action'] === 'block') {
        securityAgentLog($report, $pdo);
    }

    if ($report['action'] === 'block') {
        $reasons = array_column($report['threats'], 'type');
        securityBlockIp(
            $report['ip'],
            'AI Agent: ' . implode(', ', array_slice($reasons, 0, 3)),
            SECURITY_AUTO_BLOCK_DURATION
        );
        return false;
    }

    return true;
}

function securityRenderBlockPage() {
    http_response_code(403);
    header('Content-Type: text/html; charset=UTF-8');
    $ip = htmlspecialchars(securityGetClientIp());
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
    echo '<title>Access Blocked | Zionite Charity Security</title>';
    echo '<style>body{font-family:system-ui,sans-serif;background:#1a365d;color:#fff;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;padding:20px;}';
    echo '.box{max-width:520px;background:rgba(255,255,255,.08);padding:40px;border-radius:12px;text-align:center;border:1px solid rgba(212,168,83,.4);}';
    echo 'h1{color:#f6e05e;margin:0 0 12px;}p{opacity:.9;line-height:1.6;}small{opacity:.6;font-size:12px;}</style></head><body>';
    echo '<div class="box"><h1>&#128737; Access Blocked</h1>';
    echo '<p>Our <strong>AI Security Agent</strong> detected suspicious activity and blocked this request to protect the site.</p>';
    echo '<p>If you believe this is an error, please contact the site administrator.</p>';
    echo '<small>Reference: ' . $ip . ' &bull; ' . date('Y-m-d H:i:s') . ' UTC</small></div></body></html>';
    exit;
}
