<?php

use PDO;
use PDOException;

require_once __DIR__ . '/Database.php';

class EmailMessage {
    private $db;
    private $table = 'EMAIL_MESSAGES';

    public function __construct() {
        $database = new \Database();
        $this->db = $database->getConnection();
    }

    /**
     * Salvează un nou mesaj în baza de date
     */
    public function saveMessage($fromUserId, $toUserId, $subject, $message, $type = 'general', $projectId = null) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO {$this->table} (from_user_id, to_user_id, subject, message, type, project_id, sent_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $success = $stmt->execute([$fromUserId, $toUserId, $subject, $message, $type, $projectId]);
            
            if ($success) {
                error_log("Mesaj salvat în DB: de la user $fromUserId către user $toUserId, subiect: $subject");
            }
            
            return $success;
        } catch (PDOException $e) {
            error_log("Eroare la salvarea mesajului în DB: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obține mesajele din inbox pentru un utilizator
     */
    public function getInboxMessages($userId, $limit = 50) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    em.*,
                    CASE 
                        WHEN u.prenume IS NOT NULL AND u.nume_familie IS NOT NULL 
                        THEN CONCAT(u.prenume, ' ', u.nume_familie)
                        ELSE COALESCE(u.username, u.email)
                    END as sender_name,
                    u.email as sender_email,
                    u.username as sender_username,
                    p.title as project_name,
                    p.id as project_id_info
                FROM {$this->table} em
                JOIN USER u ON em.from_user_id = u.id
                LEFT JOIN PROIECT p ON em.project_id = p.id
                WHERE em.to_user_id = ? 
                  AND em.deleted_by_recipient = FALSE
                ORDER BY em.sent_at DESC
                LIMIT ?
            ");
            
            $stmt->execute([$userId, $limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Eroare la obținerea mesajelor inbox: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obține mesajele trimise de un utilizator
     */
    public function getSentMessages($userId, $limit = 50) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    em.*,
                    CASE 
                        WHEN u.prenume IS NOT NULL AND u.nume_familie IS NOT NULL 
                        THEN CONCAT(u.prenume, ' ', u.nume_familie)
                        ELSE COALESCE(u.username, u.email)
                    END as recipient_name,
                    u.email as recipient_email,
                    u.username as recipient_username,
                    p.title as project_name
                FROM {$this->table} em
                JOIN USER u ON em.to_user_id = u.id
                LEFT JOIN PROIECT p ON em.project_id = p.id
                WHERE em.from_user_id = ? 
                  AND em.deleted_by_sender = FALSE
                ORDER BY em.sent_at DESC
                LIMIT ?
            ");
            
            $stmt->execute([$userId, $limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Eroare la obținerea mesajelor trimise: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obține un mesaj specific cu toate detaliile
     */
    public function getMessage($messageId, $userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    em.*,
                    CASE 
                        WHEN sender.prenume IS NOT NULL AND sender.nume_familie IS NOT NULL 
                        THEN CONCAT(sender.prenume, ' ', sender.nume_familie)
                        ELSE COALESCE(sender.username, sender.email)
                    END as sender_name,
                    sender.email as sender_email,
                    sender.username as sender_username,
                    CASE 
                        WHEN recipient.prenume IS NOT NULL AND recipient.nume_familie IS NOT NULL 
                        THEN CONCAT(recipient.prenume, ' ', recipient.nume_familie)
                        ELSE COALESCE(recipient.username, recipient.email)
                    END as recipient_name,
                    recipient.email as recipient_email,
                    recipient.username as recipient_username,
                    p.title as project_name,
                    p.descriere as project_description
                FROM {$this->table} em
                JOIN USER sender ON em.from_user_id = sender.id
                JOIN USER recipient ON em.to_user_id = recipient.id
                LEFT JOIN PROIECT p ON em.project_id = p.id
                WHERE em.id = ? 
                  AND (em.from_user_id = ? OR em.to_user_id = ?)
            ");
            
            $stmt->execute([$messageId, $userId, $userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Eroare la obținerea mesajului: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Marchează un mesaj ca citit
     */
    public function markAsRead($messageId, $userId) {
        try {
            $stmt = $this->db->prepare("
                UPDATE {$this->table} 
                SET is_read = 1, read_at = NOW(), updated_at = NOW()
                WHERE id = ? AND to_user_id = ?
            ");
            
            return $stmt->execute([$messageId, $userId]);
        } catch (PDOException $e) {
            error_log("Eroare la marcarea ca citit: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Numărul de mesaje necitite pentru un utilizator
     */
    public function getUnreadCount($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM {$this->table} 
                WHERE to_user_id = ? 
                  AND is_read = 0 
                  AND deleted_by_recipient = FALSE
            ");
            
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $result['count'];
        } catch (PDOException $e) {
            error_log("Eroare la obținerea numărului de mesaje necitite: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Șterge un mesaj (soft delete)
     */
    public function deleteMessage($messageId, $userId) {
        try {
            $stmt = $this->db->prepare("
                UPDATE {$this->table} 
                SET deleted_by_recipient = CASE WHEN to_user_id = ? THEN 1 ELSE deleted_by_recipient END,
                    deleted_by_sender = CASE WHEN from_user_id = ? THEN 1 ELSE deleted_by_sender END,
                    updated_at = NOW()
                WHERE id = ? AND (from_user_id = ? OR to_user_id = ?)
            ");
            
            return $stmt->execute([$userId, $userId, $messageId, $userId, $userId]);
        } catch (PDOException $e) {
            error_log("Eroare la ștergerea mesajului: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obține conversația între 2 utilizatori
     */
    public function getConversation($user1Id, $user2Id, $limit = 20) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    em.*,
                    CASE 
                        WHEN sender.prenume IS NOT NULL AND sender.nume_familie IS NOT NULL 
                        THEN CONCAT(sender.prenume, ' ', sender.nume_familie)
                        ELSE COALESCE(sender.username, sender.email)
                    END as sender_name,
                    sender.email as sender_email,
                    p.title as project_name
                FROM {$this->table} em
                JOIN USER sender ON em.from_user_id = sender.id
                LEFT JOIN PROIECT p ON em.project_id = p.id
                WHERE ((em.from_user_id = ? AND em.to_user_id = ?) 
                    OR (em.from_user_id = ? AND em.to_user_id = ?))
                  AND em.deleted_by_sender = FALSE 
                  AND em.deleted_by_recipient = FALSE
                ORDER BY em.sent_at ASC
                LIMIT ?
            ");
            
            $stmt->execute([$user1Id, $user2Id, $user2Id, $user1Id, $limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Eroare la obținerea conversației: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obține mesajele pentru un proiect specific
     */
    public function getProjectMessages($projectId, $limit = 50) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    em.*,
                    CASE 
                        WHEN sender.prenume IS NOT NULL AND sender.nume_familie IS NOT NULL 
                        THEN CONCAT(sender.prenume, ' ', sender.nume_familie)
                        ELSE COALESCE(sender.username, sender.email)
                    END as sender_name,
                    sender.email as sender_email,
                    CASE 
                        WHEN recipient.prenume IS NOT NULL AND recipient.nume_familie IS NOT NULL 
                        THEN CONCAT(recipient.prenume, ' ', recipient.nume_familie)
                        ELSE COALESCE(recipient.username, recipient.email)
                    END as recipient_name,
                    recipient.email as recipient_email,
                    p.title as project_name
                FROM {$this->table} em
                JOIN USER sender ON em.from_user_id = sender.id
                JOIN USER recipient ON em.to_user_id = recipient.id
                JOIN PROIECT p ON em.project_id = p.id
                WHERE em.project_id = ?
                ORDER BY em.sent_at DESC
                LIMIT ?
            ");
            
            $stmt->execute([$projectId, $limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Eroare la obținerea mesajelor proiectului: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Statistici pentru admin
     */
    public function getStatistics($dateFrom = null, $dateTo = null) {
        try {
            $whereClause = '';
            $params = [];
            
            if ($dateFrom && $dateTo) {
                $whereClause = 'WHERE sent_at BETWEEN ? AND ?';
                $params = [$dateFrom, $dateTo];
            }
            
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_messages,
                    COUNT(CASE WHEN is_read = 0 THEN 1 END) as unread_messages,
                    COUNT(CASE WHEN type = 'admin' THEN 1 END) as admin_messages,
                    COUNT(CASE WHEN type = 'general' THEN 1 END) as general_messages,
                    COUNT(CASE WHEN type = 'project' THEN 1 END) as project_messages,
                    COUNT(CASE WHEN DATE(sent_at) = CURDATE() THEN 1 END) as today_messages
                FROM {$this->table}
                $whereClause
            ");
            
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Eroare la obținerea statisticilor mesaje: " . $e->getMessage());
            return [
                'total_messages' => 0,
                'unread_messages' => 0,
                'admin_messages' => 0,
                'general_messages' => 0,
                'project_messages' => 0,
                'today_messages' => 0
            ];
        }
    }

    /**
     * Căutare în mesaje
     */
    public function searchMessages($userId, $searchTerm, $limit = 20) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    em.*,
                    CASE 
                        WHEN sender.prenume IS NOT NULL AND sender.nume_familie IS NOT NULL 
                        THEN CONCAT(sender.prenume, ' ', sender.nume_familie)
                        ELSE COALESCE(sender.username, sender.email)
                    END as sender_name,
                    sender.email as sender_email,
                    p.title as project_name
                FROM {$this->table} em
                JOIN USER sender ON em.from_user_id = sender.id
                LEFT JOIN PROIECT p ON em.project_id = p.id
                WHERE (em.from_user_id = ? OR em.to_user_id = ?)
                  AND (em.subject LIKE ? OR em.message LIKE ?)
                  AND em.deleted_by_sender = FALSE 
                  AND em.deleted_by_recipient = FALSE
                ORDER BY em.sent_at DESC
                LIMIT ?
            ");
            
            $searchPattern = '%' . $searchTerm . '%';
            $stmt->execute([$userId, $userId, $searchPattern, $searchPattern, $limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Eroare la căutarea în mesaje: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Marchează toate mesajele ca citite pentru un utilizator
     */
    public function markAllAsRead($userId) {
        try {
            $stmt = $this->db->prepare("
                UPDATE {$this->table} 
                SET is_read = 1, read_at = NOW(), updated_at = NOW()
                WHERE to_user_id = ? AND is_read = 0 AND deleted_by_recipient = FALSE
            ");
            
            return $stmt->execute([$userId]);
        } catch (PDOException $e) {
            error_log("Eroare la marcarea tuturor mesajelor ca citite: " . $e->getMessage());
            return false;
        }
    }
}