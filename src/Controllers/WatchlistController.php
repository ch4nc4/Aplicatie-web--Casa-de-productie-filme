<?php
// filepath: /home/ralu/Aplicatie-web--Casa-de-productie-filme/src/Controllers/WatchlistController.php

require_once __DIR__ . '/../Models/WatchlistItemModel.php';

class WatchlistController {
    private $watchlistModel;

    public function __construct() {
        $this->watchlistModel = new WatchlistItem();
    }

    public function index($userId) {
        $watchlistItems = $this->watchlistModel->getAllByUser($userId);
        require __DIR__ . '/../Views/watchlist/index.php';
    }

    public function add() {
        session_start(); // <-- Adaugă această linie!

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF validation
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die('CSRF token validation failed');
            }
            
            $userId = $_SESSION['user_id'];
            $projectId = trim($_POST['project_id'] ?? '');

            if (empty($userId)) {
                $error = "Utilizatorul este obligatoriu.";
                require __DIR__ . '/../Views/watchlist/create.php';
                return;
            }

            if (empty($projectId)) {
                $error = "Proiectul este obligatoriu.";
                require __DIR__ . '/../Views/watchlist/create.php';
                return;
            }

            if ($this->watchlistModel->exists($userId, $projectId)) {
                $error = "Acest proiect este deja în watchlist pentru utilizatorul selectat.";
                require __DIR__ . '/../Views/watchlist/create.php';
                return;
            }

            try {
                if ($this->watchlistModel->add($userId, $projectId)) {
                    $_SESSION['success'] = "Element adăugat în watchlist cu succes!";
                    header("Location: /watchlist?user_id=" . urlencode($userId));
                    exit;
                } else {
                    $error = "Eroare la adăugarea elementului în watchlist.";
                    require __DIR__ . '/../Views/watchlist/create.php';
                }
            } catch (Exception $e) {
                $error = "Eroare de sistem: " . $e->getMessage();
                require __DIR__ . '/../Views/watchlist/create.php';
            }
        } else {
            header('Location: /watchlist/create');
            exit;
        }
    }

    public function remove() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF validation
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die('CSRF token validation failed');
            }
            
            $userId = $_POST['user_id'] ?? null;
            $projectId = $_POST['project_id'] ?? null;
            if ($userId && $projectId) {
                $this->watchlistModel->remove($userId, $projectId);
            }
            header("Location: /watchlist?user_id=" . urlencode($userId));
            exit;
        }
    }
}