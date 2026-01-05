SET NAMES utf8mb4;
SET sql_mode = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER';

CREATE TABLE EMAIL_MESSAGES (
    id INT AUTO_INCREMENT PRIMARY KEY,
    from_user_id INT NOT NULL,
    to_user_id INT NOT NULL,
    subject VARCHAR(500) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('role_change', 'project', 'staff_message', 'general') DEFAULT 'general',
    project_id INT NULL,
    reply_to_message_id INT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_by_sender BOOLEAN DEFAULT FALSE,
    deleted_by_recipient BOOLEAN DEFAULT FALSE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign Keys corectate pentru baza ta
    CONSTRAINT fk_email_from_user FOREIGN KEY (from_user_id) REFERENCES USER(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_email_to_user FOREIGN KEY (to_user_id) REFERENCES USER(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_email_project FOREIGN KEY (project_id) REFERENCES PROIECT(id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_email_reply FOREIGN KEY (reply_to_message_id) REFERENCES EMAIL_MESSAGES(id) ON DELETE SET NULL ON UPDATE CASCADE
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Indexes pentru performanță
CREATE INDEX idx_email_to_user ON EMAIL_MESSAGES(to_user_id);
CREATE INDEX idx_email_from_user ON EMAIL_MESSAGES(from_user_id);
CREATE INDEX idx_email_sent_at ON EMAIL_MESSAGES(sent_at);
CREATE INDEX idx_email_unread ON EMAIL_MESSAGES(to_user_id, is_read);
CREATE INDEX idx_email_conversation ON EMAIL_MESSAGES(from_user_id, to_user_id);
CREATE INDEX idx_email_type ON EMAIL_MESSAGES(type);
CREATE INDEX idx_email_project ON EMAIL_MESSAGES(project_id);