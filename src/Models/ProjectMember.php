<?php
// filepath: /home/ralu/Aplicatie-web--Casa-de-productie-filme/src/Models/ProjectMember.php

class ProjectMember {
    private $db;

    public function __construct($db = null) {
        if ($db) {
            $this->db = $db;
        } else {
            require_once __DIR__ . '/../../config/database.php';
            $this->db = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
                DB_USER,
                DB_PASS,
                PDO_OPTIONS
            );
        }
    }

    public function getAll() {
        $sql = "SELECT * FROM MEMBRU_PROIECT";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT * FROM MEMBRU_PROIECT WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $sql = "INSERT INTO MEMBRU_PROIECT (id_proiect, id_user, tip_echipa, assigned_at, expires_at) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['id_proiect'],
            $data['id_user'],
            $data['tip_echipa'] ?? null,
            $data['assigned_at'] ?? date('Y-m-d H:i:s'),
            $data['expires_at'] ?? null
        ]);
    }

    public function update($id, $data) {
        $sql = "UPDATE MEMBRU_PROIECT SET id_proiect = ?, id_user = ?, tip_echipa = ?, assigned_at = ?, expires_at = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['id_proiect'],
            $data['id_user'],
            $data['tip_echipa'] ?? null,
            $data['assigned_at'] ?? date('Y-m-d H:i:s'),
            $data['expires_at'] ?? null,
            $id
        ]);
    }


    public function exists($id_proiect, $id_user) {
        $sql = "SELECT COUNT(*) FROM MEMBRU_PROIECT WHERE id_proiect = ? AND id_user = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_proiect, $id_user]);
        return $stmt->fetchColumn() > 0;
    }

    public function delete($id) {
        $sql = "DELETE FROM MEMBRU_PROIECT WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }


    public function getByUserId($userId) {
        $sql = "SELECT mp.id as membership_id, p.title, p.id FROM MEMBRU_PROIECT mp JOIN PROIECT p ON mp.id_proiect = p.id WHERE mp.id_user = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

   
    public function isMember($userId, $projectId) {
        $sql = "SELECT COUNT(*) FROM MEMBRU_PROIECT WHERE id_user = ? AND id_proiect = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $projectId]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Obține toți membrii proiectelor cu detalii pentru export
     */
    public function getAllWithDetails() {
        try {
            $sql = "
                SELECT 
                    pm.*,
                    p.title as project_title,
                    p.tip as project_type,
                    sp.nume as project_status,
                    CONCAT(u.prenume, ' ', u.nume_familie) as member_name,
                    u.email as member_email,
                    pm.assigned_at as joined_at,
                    CASE 
                        WHEN u.deleted_at IS NULL THEN 'Activ'
                        ELSE 'Inactiv'
                    END as member_status
                FROM MEMBRU_PROIECT pm
                JOIN PROIECT p ON pm.id_proiect = p.id
                JOIN USER u ON pm.id_user = u.id
                LEFT JOIN STATUS_PROIECT sp ON p.id_status = sp.id
                ORDER BY p.title, u.nume_familie, u.prenume
            ";
            
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Eroare la obținerea membrilor proiectelor cu detalii: " . $e->getMessage());
            return [];
        }
    }
}