<?php

require_once __DIR__ . '/Database.php';

class User {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // CREATE 
    public function create($data) {
        $sql = "INSERT INTO USER (prenume, nume_familie, email, hash_parola, numar_telefon) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['prenume'],
            $data['nume_familie'],
            $data['email'],
            $data['hash_parola'] ?? null,
            $data['numar_telefon'] ?? null,
        ]);
    }
    
    // READ - gaseste toti userii
    public function getAll() {
        $sql = "SELECT * FROM USER ORDER BY prenume ASC, nume_familie ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    // READ - gaseste user dupa ID
    public function getById($id) {
        $sql = "SELECT * FROM USER WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    // READ - gaseste user dupa email
    public function getByEmail($email) {
        $sql = "SELECT * FROM USER WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    // UPDATE - Update user
    public function update($id, $data) {
        $sql = "UPDATE USER SET 
            prenume = ?, 
            nume_familie = ?, 
            username = ?, 
            bio = ?, 
            avatar_url = ?, 
            numar_telefon = ?
            WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['prenume'],
            $data['nume_familie'],
            $data['username'],
            $data['bio'],
            $data['avatar_url'],
            $data['numar_telefon'],
            $id
        ]);
    }
    
    // DELETE - sterge user
    public function delete($id) {
        $sql = "DELETE FROM USER WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    // READ - gaseste userii dupa rol
    public function getByRole($rol) {
        $sql = "SELECT * FROM USER WHERE rol = ? ORDER BY prenume ASC, nume_familie ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$rol]);
        return $stmt->fetchAll();
    }
    
    // READ - gaseste proiectele create de un user
    public function getUserProjects($userId) {
        $sql = "SELECT p.*, s.nume as status_name 
                FROM PROIECT p 
                LEFT JOIN STATUS_PROIECT s ON p.id_status = s.id
                WHERE p.contribuitor = ?
                ORDER BY p.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    // READ - gaseste proiectele unde userul este membru
    public function getUserMemberships($userId) {
        $sql = "SELECT p.*, mp.tip_echipa, mp.assigned_at, mp.expires_at, s.nume as status_name
                FROM MEMBRU_PROIECT mp
                JOIN PROIECT p ON mp.id_proiect = p.id
                LEFT JOIN STATUS_PROIECT s ON p.id_status = s.id
                WHERE mp.id_user = ?
                ORDER BY mp.assigned_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    // READ - verifica daca emailul exista 
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM USER WHERE email = ?";
        $params = [$email];
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
    
    // READ - gaseste toti userii cu numarul de proiecte create
    public function getAllWithProjectCount() {
        $sql = "SELECT u.*, COUNT(p.id) as projects_count 
                FROM USER u 
                LEFT JOIN PROIECT p ON u.id = p.contribuitor 
                GROUP BY u.id 
                ORDER BY u.prenume ASC, u.nume_familie ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    // READ - obtine numele complet al userului
    public function getFullName($userId) {
        $user = $this->getById($userId);
        if ($user) {
            return $user['prenume'] . ' ' . $user['nume_familie'];
        }
        return null;
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM USER WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM USER WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePassword($id, $hashedPassword) {
        $stmt = $this->db->prepare("UPDATE USER SET hash_parola = ? WHERE id = ?");
        return $stmt->execute([$hashedPassword, $id]);
    }

   
    public function getCrewMembers() {
        $sql = "
            SELECT 
                U.id, U.prenume, U.nume_familie, U.email, ROL.nume AS rol
            FROM USER U
            JOIN ROL_USER RU ON RU.id_user = U.id
            JOIN ROL ON RU.id_rol = ROL.id
            WHERE ROL.nume IN ('Lider productie', 'Staff productie')
            GROUP BY U.id, ROL.nume, U.nume_familie, U.prenume
            ORDER BY U.nume_familie, U.prenume
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}