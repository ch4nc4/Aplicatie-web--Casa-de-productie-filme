<?php

class Database {
    private $pdo;
    
    public function __construct() {
        require_once __DIR__ . '/../../config/database.php';
    }
    
    public function getConnection() {
        if ($this->pdo === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                $this->pdo = new PDO($dsn, DB_USER, DB_PASS, PDO_OPTIONS);
            } catch (PDOException $e) {
                throw new Exception("Database connection failed: " . $e->getMessage());
            }
        }
        
        return $this->pdo;
    }
    
    public function testConnection() {
        try {
            $pdo = $this->getConnection();
            return $pdo !== null;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function closeConnection() {
        $this->pdo = null;
    }
    
    public function getDatabaseInfo() {
        try {
            $pdo = $this->getConnection();
            $stmt = $pdo->query("SELECT DATABASE() as dbname, VERSION() as version");
            return $stmt->fetch();
        } catch (Exception $e) {
            return null;
        }
    }
}