CREATE DATABASE IF NOT EXISTS clipboard_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE clipboard_db;
CREATE TABLE IF NOT EXISTS clipboard_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) DEFAULT '',
    content TEXT NOT NULL,
    created_at DATETIME NOT NULL,
    expires_at DATETIME NOT NULL,
    INDEX idx_expires_at (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;