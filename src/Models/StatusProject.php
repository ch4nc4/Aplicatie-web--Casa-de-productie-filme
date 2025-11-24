<?php

require_once __DIR__ . '/Database.php';

class StatusProject {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // CREATE 
    public function create($data) {
        $sql = "INSERT INTO STATUS_PROIECT (nume, data_start, data_finalizare, nota_aditionala) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['nume'],
            $data['data_start'],
            $data['data_finalizare'] ?? null,
            $data['nota_aditionala'] ?? null
        ]);
    }
    
    // READ - gaseste toate statusurile
    public function getAll() {
        $sql = "SELECT * FROM STATUS_PROIECT ORDER BY id ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    // READ - gaseste status dupa ID
    public function getById($id) {
        $sql = "SELECT * FROM STATUS_PROIECT WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    // READ - gaseste status dupa nume
    public function getByName($nume) {
        $sql = "SELECT * FROM STATUS_PROIECT WHERE nume = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$nume]);
        return $stmt->fetch();
    }
    
    // UPDATE - update status
    public function update($id, $data) {
        $sql = "UPDATE STATUS_PROIECT SET nume = ?, data_start = ?, data_finalizare = ?, nota_aditionala = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['nume'],
            $data['data_start'],
            $data['data_finalizare'] ?? null,
            $data['nota_aditionala'] ?? null,
            $id
        ]);
    }
    
    // DELETE - sterge status
    public function delete($id) {
        $sql = "DELETE FROM STATUS_PROIECT WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    // READ - numara proiectele cu un anumit status
    public function getProjectsCount($statusId) {
        $sql = "SELECT COUNT(*) as count FROM PROIECT WHERE id_status = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$statusId]);
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    // READ - toate statusurile cu numarul de proiecte asociate
    public function getAllWithProjectCount() {
        $sql = "SELECT s.*, COUNT(p.id) as projects_count 
                FROM STATUS_PROIECT s 
                LEFT JOIN PROIECT p ON s.id = p.id_status 
                GROUP BY s.id 
                ORDER BY s.id ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}