-- Zionite AI Security Agent — event log table (auto-created by the app)
USE zionite_charity;

CREATE TABLE IF NOT EXISTS security_events (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
