CREATE TABLE IF NOT EXISTS legal_sections (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    page ENUM('terms', 'privacy') NOT NULL,
    sort_order INT UNSIGNED DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_page (page),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS legal_section_translations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    section_id INT UNSIGNED NOT NULL,
    language_id INT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    UNIQUE KEY unique_legal_lang (section_id, language_id),
    FOREIGN KEY (section_id) REFERENCES legal_sections(id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES languages(id) ON DELETE CASCADE
) ENGINE=InnoDB;
