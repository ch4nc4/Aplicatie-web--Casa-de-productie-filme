<?php
// filepath: /home/ralu/Aplicatie-web--Casa-de-productie-filme/src/Models/Role.php

class Role {
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
        $sql = "SELECT * FROM ROL";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT * FROM ROL WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByName($name) {
        $sql = "SELECT * FROM ROL WHERE nume = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$name]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $sql = "INSERT INTO ROL (nume, descriere) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['nume'],
            $data['descriere'] ?? null
        ]);
    }

    public function update($id, $data) {
        $sql = "UPDATE ROL SET nume = ?, descriere = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['nume'],
            $data['descriere'] ?? null,
            $id
        ]);
    }

    public function delete($id) {
        $sql = "DELETE FROM ROL WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}