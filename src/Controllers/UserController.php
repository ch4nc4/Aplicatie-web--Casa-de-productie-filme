<?php
require_once __DIR__ . '/../Models/User.php';

class UserController {
    public function profile() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        $userModel = new User();
        $user = $userModel->getById($_SESSION['user_id']);
        require __DIR__ . '/../Views/users/profile.php';
    }

    public function updateProfile() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        // CSRF validation
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            die('CSRF token validation failed');
        }
        
        $userModel = new User();
        $userId = $_SESSION['user_id'];
        $user = $userModel->getById($userId); 

        // Preluare date din formular
        $data = [
            'email' => $user['email'],
            'prenume' => $_POST['prenume'] ?? $user['prenume'],
            'nume_familie' => $_POST['nume_familie'] ?? $user['nume_familie'],
            'username' => $_POST['username'] ?? $user['username'],
            'bio' => $_POST['bio'] ?? $user['bio'],
            'avatar_url' => $_POST['avatar_url'] ?? $user['avatar_url'],
            'numar_telefon' => $_POST['numar_telefon'] ?? $user['numar_telefon']
        ];

        $success = false;
        $error = false;
        if ($userModel->update($userId, $data)) {
            $success = "Profil actualizat cu succes!";
        } else {
            $error = "Eroare la actualizarea profilului.";
        }
        $user = $userModel->getById($userId);
        require __DIR__ . '/../Views/users/profile.php';
    }
}