<?php
// filepath: /home/ralu/Aplicatie-web--Casa-de-productie-filme/src/Models/RoleUser.php

class RoleUser {
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

    
    public function getDb() {
        return $this->db;
    }

    public function getAll() {
        $sql = "SELECT * FROM ROL_USER";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT * FROM ROL_USER WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $sql = "INSERT INTO ROL_USER (id_user, id_rol, assigned_at, expires_at) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['id_user'],
            $data['id_rol'],
            $data['assigned_at'] ?? date('Y-m-d H:i:s'),
            $data['expires_at'] ?? null
        ]);
    }

    public function update($id, $data) {
        $sql = "UPDATE ROL_USER SET id_user = ?, id_rol = ?, assigned_at = ?, expires_at = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['id_user'],
            $data['id_rol'],
            $data['assigned_at'] ?? date('Y-m-d H:i:s'),
            $data['expires_at'] ?? null,
            $id
        ]);
    }

    public function exists($id_user, $id_rol) {
        $sql = "SELECT COUNT(*) FROM ROL_USER WHERE id_user = ? AND id_rol = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_user, $id_rol]);
        return $stmt->fetchColumn() > 0;
    }

    public function delete($id) {
        $sql = "DELETE FROM ROL_USER WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }


    public function getByUserId($userId) {
        $sql = "SELECT ROL.id, ROL.nume, ROL.descriere
                FROM ROL_USER
                JOIN ROL ON ROL_USER.id_rol = ROL.id
                WHERE ROL_USER.id_user = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}