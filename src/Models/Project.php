<?php

require_once __DIR__ . '/Database.php';

class Project {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // CREATE 
    public function create($data) {
        $sql = "INSERT INTO PROIECT (tip, title, buget, descriere, id_status, durata_derulare, poster_url, contribuitor) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['tip'],
            $data['title'], 
            $data['buget'],
            $data['descriere'],
            $data['id_status'],
            $data['durata_derulare'],
            $data['poster_url'],
            $data['contribuitor']
        ]);
    }
    
    // READ - toate proiectele
    public function getAll() {
        $sql = "SELECT p.*, s.nume as status_name, u.prenume, u.nume_familie 
                FROM PROIECT p 
                LEFT JOIN STATUS_PROIECT s ON p.id_status = s.id
                LEFT JOIN USER u ON p.contribuitor = u.id
                ORDER BY p.id DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    // READ - Gaseste proiectul dupa ID
    public function getById($id) {
        $sql = "SELECT p.*, s.nume as status_name, u.prenume, u.nume_familie 
                FROM PROIECT p 
                LEFT JOIN STATUS_PROIECT s ON p.id_status = s.id
                LEFT JOIN USER u ON p.contribuitor = u.id
                WHERE p.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    // UPDATE - update proiect
    public function update($id, $data) {
        $sql = "UPDATE PROIECT 
                SET tip = ?, title = ?, buget = ?, descriere = ?, id_status = ?, 
                    durata_derulare = ?, poster_url = ?, contribuitor = ?
                WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['tip'],
            $data['title'],
            $data['buget'],
            $data['descriere'],
            $data['id_status'] ?? null,
            $data['durata_derulare'] ?? null,
            $data['poster_url'] ?? null,
            $data['contribuitor'] ?? null,  // ADDED - was missing!
            $id
        ]);
    }
    
    // DELETE - sterge proiect
    public function delete($id) {
        $sql = "DELETE FROM PROIECT WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    // READ - gaseste proiecte dupa status
    public function getByStatus($statusId) {
        $sql = "SELECT p.*, s.nume as status_name 
                FROM PROIECT p 
                LEFT JOIN STATUS_PROIECT s ON p.id_status = s.id
                WHERE p.id_status = ?
                ORDER BY p.id DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$statusId]);
        return $stmt->fetchAll();
    }
    
    // READ - gaseste proiecte dupa contributor
    public function getByContributor($contributorId) {
        $sql = "SELECT p.*, s.nume as status_name 
                FROM PROIECT p 
                LEFT JOIN STATUS_PROIECT s ON p.id_status = s.id
                WHERE p.contribuitor = ?
                ORDER BY p.id DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$contributorId]);
        return $stmt->fetchAll();
    }
    
    // READ - gaseste membri proiect
    public function getProjectMembers($projectId) {
        $sql = "SELECT u.*, mp.tip_echipa, mp.assigned_at, mp.expires_at
                FROM MEMBRU_PROIECT mp
                JOIN USER u ON mp.id_user = u.id
                WHERE mp.id_proiect = ?
                ORDER BY mp.assigned_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$projectId]);
        return $stmt->fetchAll();
    }
    
    // INSERT - adauga membru la proiect
    public function addMember($projectId, $userId, $tipEchipa = null) {
        $sql = "INSERT INTO MEMBRU_PROIECT (id_proiect, id_user, tip_echipa) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$projectId, $userId, $tipEchipa]);
    }
    
    // DELETE - elimina membru din proiect
    public function removeMember($projectId, $userId) {
        $sql = "DELETE FROM MEMBRU_PROIECT WHERE id_proiect = ? AND id_user = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$projectId, $userId]);
    }
    
    // READ - gaseste rapoarte financiare ale proiectului
    public function getFinancialReports($projectId) {
        $sql = "SELECT * FROM RAPORT_FINANCIAR WHERE id_proiect = ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$projectId]);
        return $stmt->fetchAll();
    }
}