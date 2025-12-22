<?php
// filepath: /home/ralu/Aplicatie-web--Casa-de-productie-filme/src/Controllers/RoleController.php

require_once __DIR__ . '/../Models/Role.php';

class RoleController {
    private $roleModel;

    public function __construct() {
        $this->roleModel = new Role();
    }

    public function index() {
        $roles = $this->roleModel->getAll();
        require __DIR__ . '/../Views/roles/index.php';
    }

    public function create() {
        require __DIR__ . '/../Views/roles/create.php';
    }

   
    public function createRole() {
        // CSRF validation
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            die('CSRF token validation failed');
        }
        
        $name = trim($_POST['nume'] ?? '');
        $descriere = trim($_POST['descriere'] ?? '');
        if ($name) {
            $this->roleModel->create([
                'nume' => $name,
                'descriere' => $descriere
            ]);
            header('Location: /admin/users');
            exit;
        }
    }

    public function deleteRole() {
        // CSRF validation
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            die('CSRF token validation failed');
        }
        
        $roleId = $_POST['role_id'] ?? null;
        if ($roleId) {
            $this->roleModel->delete($roleId);
            header('Location: /admin/users');
            exit;
        }
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF validation
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die('CSRF token validation failed');
            }
            
            $data = [
                'nume' => $_POST['nume'] ?? '',
                'descriere' => $_POST['descriere'] ?? ''
            ];

            if (empty($data['nume'])) {
                $error = "Numele rolului este obligatoriu.";
                require __DIR__ . '/../Views/roles/create.php';
                return;
            }

            if ($this->roleModel->create($data)) {
                header('Location: /admin/users');
                exit;
            } else {
                $error = "Eroare la crearea rolului.";
                require __DIR__ . '/../Views/roles/create.php';
            }
        }
    }

    public function show($id) {
        $role = $this->roleModel->getById($id);
        if (!$role) {
            header("HTTP/1.0 404 Not Found");
            die("Rolul nu a fost găsit");
        }
        require __DIR__ . '/../Views/roles/show.php';
    }

    public function edit($id) {
        $role = $this->roleModel->getById($id);
        if (!$role) {
            $error = "Rolul nu a fost găsit.";
            require __DIR__ . '/../Views/roles/edit.php';
            return;
        }
        require __DIR__ . '/../Views/roles/edit.php';
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF validation
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die('CSRF token validation failed');
            }
            
            $data = [
                'nume' => $_POST['nume'] ?? '',
                'descriere' => $_POST['descriere'] ?? ''
            ];

            if (empty($data['nume'])) {
                $error = "Numele rolului este obligatoriu.";
                $role = $this->roleModel->getById($id);
                require __DIR__ . '/../Views/roles/edit.php';
                return;
            }

            if ($this->roleModel->update($id, $data)) {
                header('Location: /roles');
                exit;
            } else {
                $error = "Eroare la actualizarea rolului.";
                $role = $this->roleModel->getById($id);
                require __DIR__ . '/../Views/roles/edit.php';
            }
        }
    }

    public function delete($id) {
        $role = $this->roleModel->getById($id);
        if (!$role) {
            $error = "Rolul nu a fost găsit.";
            require __DIR__ . '/../Views/roles/delete.php';
            return;
        }
        require __DIR__ . '/../Views/roles/delete.php';
    }

    public function destroy($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF validation
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die('CSRF token validation failed');
            }
            
            if ($this->roleModel->delete($id)) {
                header('Location: /roles');
                exit;
            } else {
                $error = "Eroare la ștergerea rolului.";
                require __DIR__ . '/../Views/roles/delete.php';
            }
        }
    }
}