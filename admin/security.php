<?php
/**
 * Zionite AI Security Agent — Admin dashboard
 */

require_once '../includes/config.php';
requireAdminLogin();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    securityRequireCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'unblock_ip' && !empty($_POST['ip'])) {
        securityUnblockIp(trim($_POST['ip']));
        $message = 'IP address unblocked successfully.';
        $messageType = 'success';
    }
}

$blockedIps = securityLoadBlockedIps();
$events = [];
$stats = ['blocked' => count($blockedIps), 'attacks_24h' => 0, 'high_threats' => 0];

securityEnsureTables($pdo);

try {
    $stmt = $pdo->query("SELECT * FROM security_events ORDER BY created_at DESC LIMIT 100");
    $events = $stmt->fetchAll();

    $stmt = $pdo->query("SELECT COUNT(*) FROM security_events WHERE created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
    $stats['attacks_24h'] = (int) $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM security_events WHERE threat_score >= " . (int) SECURITY_BLOCK_THRESHOLD . " AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
    $stats['high_threats'] = (int) $stmt->fetchColumn();
} catch (PDOException $e) {
    // Table may not exist yet
}

$logFile = SECURITY_STORAGE_PATH . '/events.log';
$fileEvents = [];
if (file_exists($logFile)) {
    $lines = array_slice(array_filter(explode("\n", file_get_contents($logFile))), -50);
    foreach (array_reverse($lines) as $line) {
        $row = json_decode($line, true);
        if ($row) {
            $fileEvents[] = $row;
        }
    }
}

$pageTitle = 'Security';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Security | Zionite Charity Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <style>
        body { background: var(--off-white); }
        .security-stat { background: var(--white); padding: 25px; border-radius: 10px; box-shadow: var(--shadow-md); text-align: center; }
        .security-stat h3 { font-size: 2rem; color: var(--primary-blue); margin: 10px 0; }
        .security-stat--alert h3 { color: #e53e3e; }
        .threat-badge { display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; }
        .threat-badge--block { background: #fed7d7; color: #c53030; }
        .threat-badge--log { background: #feebc8; color: #c05621; }
        .agent-banner { background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue)); color: #fff; padding: 25px; border-radius: 10px; margin-bottom: 30px; display: flex; align-items: center; gap: 20px; }
        .agent-banner i { font-size: 3rem; color: var(--accent-gold); }
    </style>
</head>
<body>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <h2><i class="fas fa-shield-alt"></i> Admin</h2>
            <nav class="admin-nav">
                <ul>
                    <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="security.php" class="active"><i class="fas fa-robot"></i> AI Security</a></li>
                    <li><a href="donations.php"><i class="fas fa-donate"></i> Donations</a></li>
                    <li><a href="messages.php"><i class="fas fa-envelope"></i> Messages</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>

        <main class="admin-content">
            <div class="admin-header">
                <h1><i class="fas fa-robot"></i> Zionite AI Security Agent</h1>
            </div>

            <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <div class="agent-banner">
                <i class="fas fa-shield-virus"></i>
                <div>
                    <h2 style="color: #fff; margin: 0 0 8px;">Active protection</h2>
                    <p style="margin: 0; opacity: 0.9;">The AI agent analyzes every request for SQL injection, XSS, bots, brute-force, and scanners. Threats are logged and high-risk IPs are blocked automatically.</p>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
                <div class="security-stat">
                    <p>Blocked IPs</p>
                    <h3><?php echo $stats['blocked']; ?></h3>
                </div>
                <div class="security-stat">
                    <p>Events (24h)</p>
                    <h3><?php echo $stats['attacks_24h']; ?></h3>
                </div>
                <div class="security-stat security-stat--alert">
                    <p>High threats blocked (24h)</p>
                    <h3><?php echo $stats['high_threats']; ?></h3>
                </div>
                <div class="security-stat">
                    <p>Block threshold</p>
                    <h3><?php echo SECURITY_BLOCK_THRESHOLD; ?>/100</h3>
                </div>
            </div>

            <div class="recent-section">
                <h3><i class="fas fa-ban"></i> Blocked IP Addresses</h3>
                <?php if (empty($blockedIps)): ?>
                <p style="color: var(--gray-600);">No IPs are currently blocked.</p>
                <?php else: ?>
                <table class="table">
                    <thead>
                        <tr><th>IP</th><th>Reason</th><th>Blocked at</th><th>Expires</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($blockedIps as $ip => $info): ?>
                        <tr>
                            <td><code><?php echo htmlspecialchars($ip); ?></code></td>
                            <td><?php echo htmlspecialchars($info['reason'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($info['blocked_at'] ?? ''); ?></td>
                            <td><?php echo !empty($info['until']) ? date('Y-m-d H:i', $info['until']) : 'Permanent'; ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <?php echo csrfField(); ?>
                                    <input type="hidden" name="action" value="unblock_ip">
                                    <input type="hidden" name="ip" value="<?php echo htmlspecialchars($ip); ?>">
                                    <button type="submit" class="btn btn-secondary" style="padding:5px 10px;font-size:0.8rem;">Unblock</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>

            <div class="recent-section">
                <h3><i class="fas fa-list"></i> Recent threat events</h3>
                <?php if (empty($events) && empty($fileEvents)): ?>
                <p style="color: var(--gray-600);">No threats logged yet.</p>
                <?php elseif (!empty($events)): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr><th>Time</th><th>IP</th><th>Score</th><th>Types</th><th>Action</th><th>URI</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($events as $ev): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($ev['created_at']); ?></td>
                                <td><code><?php echo htmlspecialchars($ev['ip_address']); ?></code></td>
                                <td><strong><?php echo (int) $ev['threat_score']; ?></strong></td>
                                <td><?php echo htmlspecialchars($ev['threat_types']); ?></td>
                                <td><span class="threat-badge threat-badge--<?php echo $ev['action_taken'] === 'block' ? 'block' : 'log'; ?>"><?php echo htmlspecialchars($ev['action_taken']); ?></span></td>
                                <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;"><?php echo htmlspecialchars($ev['request_uri']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead><tr><th>Time</th><th>IP</th><th>Score</th><th>Action</th><th>URI</th></tr></thead>
                        <tbody>
                            <?php foreach ($fileEvents as $ev): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($ev['time'] ?? ''); ?></td>
                                <td><code><?php echo htmlspecialchars($ev['ip'] ?? ''); ?></code></td>
                                <td><?php echo (int) ($ev['score'] ?? 0); ?></td>
                                <td><?php echo htmlspecialchars($ev['action'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($ev['uri'] ?? ''); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
