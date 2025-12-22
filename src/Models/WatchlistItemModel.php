<?php
// filepath: /home/ralu/Aplicatie-web--Casa-de-productie-filme/src/Models/WatchlistItem.php

require_once __DIR__ . '/Database.php';


class WatchlistItem {
    protected $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getAllByUser($userId) {
        $sql = "SELECT wi.*, p.title FROM WATCHLIST_ITEM wi
                JOIN PROIECT p ON wi.id_proiect = p.id
                WHERE wi.id_user = ?
                ORDER BY wi.added_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function exists($userId, $projectId) {
        $sql = "SELECT COUNT(*) FROM WATCHLIST_ITEM WHERE id_user = ? AND id_proiect = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $projectId]);
        return $stmt->fetchColumn() > 0;
    }

    public function add($userId, $projectId) {
        $sql = "INSERT INTO WATCHLIST_ITEM (id_user, id_proiect) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $projectId]);
    }

    public function remove($userId, $projectId) {
        $sql = "DELETE FROM WATCHLIST_ITEM WHERE id_user = ? AND id_proiect = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $projectId]);
    }
}