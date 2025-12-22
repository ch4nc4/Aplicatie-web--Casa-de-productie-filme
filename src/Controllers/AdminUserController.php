<?php
// filepath: /home/ralu/Aplicatie-web--Casa-de-productie-filme/src/Controllers/AdminUserController.php

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Role.php';
require_once __DIR__ . '/../Models/RoleUser.php';
require_once __DIR__ . '/../Models/Project.php';
require_once __DIR__ . '/../Models/ProjectMember.php';

class AdminUserController {
    public function index() {
        try {
            $userModel = new User();
            $roleModel = new Role();
            $roleUserModel = new RoleUser();
            $projectModel = new Project();
            $projectMemberModel = new ProjectMember();

            $users = $userModel->getAll();
            $roles = $roleModel->getAll();
            $projects = $projectModel->getAll();

            // For each user, get their roles and projects
            foreach ($users as &$user) {
                $user['roles'] = $roleUserModel->getByUserId($user['id']);
                $user['projects'] = $projectMemberModel->getByUserId($user['id']);
            }

            $roleUserCounts = [];
            $sql = "SELECT id_rol, COUNT(*) as user_count FROM ROL_USER GROUP BY id_rol";
            $stmt = $roleUserModel->getDb()->query($sql);
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $roleUserCounts[$row['id_rol']] = $row['user_count'];
            }

            if ($addRoleSuccess) {
                $roleUserCounts[$roleId] = ($roleUserCounts[$roleId] ?? 0) + 1;
            }

            if ($removeRoleSuccess) {
                $roleUserCounts[$roleId] = max(0, ($roleUserCounts[$roleId] ?? 1) - 1);
            }

            require __DIR__ . '/../Views/admin-users/index.php';
        } catch (Exception $e) {
            $error = "Eroare la încărcarea utilizatorilor: " . $e->getMessage();
            require __DIR__ . '/../Views/admin-users/index.php';
        }
    }

   
    public function addRole($userId) {
        $roleUserModel = new RoleUser();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF validation
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die('CSRF token validation failed');
            }
            
            $data = [
                'id_user' => $userId,
                'id_rol' => $_POST['id_rol'],
                'assigned_at' => date('Y-m-d H:i:s'),
                'expires_at' => !empty($_POST['expires_at']) ? $_POST['expires_at'] : null
            ];
          
            if ($roleUserModel->exists($data['id_user'], $data['id_rol'])) {
                $error = "Utilizatorul are deja acest rol.";
            } else {
                $roleUserModel->create($data);
            }
            header("Location: /admin/users");
            exit;
        }
    }

    public function removeRole($roleUserId) {
        // CSRF validation
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            die('CSRF token validation failed');
        }
        
        $roleUserModel = new RoleUser();
        $roleUserModel->delete($roleUserId);
        header("Location: /admin/users");
        exit;
    }

    
    public function addProjectMembership($userId) {
        $projectMemberModel = new ProjectMember();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF validation
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die('CSRF token validation failed');
            }
            
            $data = [
                'id_user' => $userId,
                'id_proiect' => $_POST['id_proiect'],
                'tip_echipa' => $_POST['tip_echipa'] ?? null,
                'assigned_at' => date('Y-m-d H:i:s'),
                'expires_at' => $_POST['expires_at'] ?? null
            ];
            $projectMemberModel->create($data);
            header("Location: /admin/users");
            exit;
        }
    }

    public function removeProjectMembership($projectMemberId) {
        // CSRF validation
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            die('CSRF token validation failed');
        }
        
        $projectMemberModel = new ProjectMember();
        $projectMemberModel->delete($projectMemberId);
        
        session_start();
        if (!in_array('Admin', $_SESSION['roles'] ?? [])) {
            header("Location: /");
            exit;
        }
        header("Location: /admin/users");
        exit;
    }
}