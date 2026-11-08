CREATE DATABASE IF NOT EXISTS diamondx_bd CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE diamondx_bd;

CREATE TABLE IF NOT EXISTS admins (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(40) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  last_login DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS settings (
  setting_key VARCHAR(100) PRIMARY KEY,
  setting_value TEXT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS search_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  uid VARCHAR(20) NOT NULL DEFAULT '',
  status VARCHAR(30) NOT NULL,
  detail VARCHAR(200) NOT NULL DEFAULT '',
  ip_address VARCHAR(45) NOT NULL DEFAULT '',
  user_agent VARCHAR(255) NOT NULL DEFAULT '',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_search_created(created_at),
  INDEX idx_search_status(status),
  INDEX idx_search_uid(uid)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS rate_limits (
  ip_address VARCHAR(45) PRIMARY KEY,
  window_start DATETIME NOT NULL,
  hits INT UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB;

INSERT INTO settings(setting_key,setting_value) VALUES
('site_name','DIAMONDX BD'),
('announcement',''),
('maintenance_mode','0'),
('player_api_url','https://auraffxserverinfocheckerapi.onrender.com/api/player')
ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value);
