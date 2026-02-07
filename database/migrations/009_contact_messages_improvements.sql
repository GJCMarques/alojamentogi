-- =============================================
-- A Casa do Gi - Contact Messages Improvements
-- Add ignored status and spam email tracking
-- =============================================

USE casadogi;

-- Add is_ignored field to contact_submissions if not exists
ALTER TABLE contact_submissions
    ADD COLUMN IF NOT EXISTS is_ignored TINYINT(1) DEFAULT 0 COMMENT 'Ignored messages' AFTER is_spam;

-- Add index for is_ignored if not exists
ALTER TABLE contact_submissions
    ADD INDEX IF NOT EXISTS idx_ignored (is_ignored);

-- Create table to track spam emails
CREATE TABLE IF NOT EXISTS spam_emails (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    reason VARCHAR(500) DEFAULT NULL COMMENT 'Why this email was marked as spam',
    blocked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
