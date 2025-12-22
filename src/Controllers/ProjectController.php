<?php

require_once __DIR__ . '/../Models/Project.php';

class ProjectController {
    private $projectModel;
    
    public function __construct() {
        $this->projectModel = new Project();
    }
    
    public function index() {
        $projects = $this->projectModel->getAll();
        include __DIR__ . '/../views/projects/index.php';
    }
    
    public function show($id) {
        $project = $this->projectModel->getById($id);
        if (!$project) {
            header("HTTP/1.0 404 Not Found");
            die("Project not found");
        }
        include __DIR__ . '/../views/projects/show.php';
    }
    
    public function create() {
        include __DIR__ . '/../views/projects/create.php';
    }
    
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF validation
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die('CSRF token validation failed');
            }
            
            $data = [
                'tip' => $_POST['tip'] ?? '',
                'title' => $_POST['title'] ?? '',
                'buget' => $_POST['buget'] ?? 0,
                'descriere' => $_POST['descriere'] ?? '',
                'id_status' => $_POST['id_status'] ?? 1,
                'durata_derulare' => $_POST['durata_derulare'] ?? null,
                'poster_url' => $_POST['poster_url'] ?? '',
                'contribuitor' => $_POST['contribuitor'] ?? 1
            ];
            
            if ($this->projectModel->create($data)) {
                header('Location: /views/projects');
            } else {
                $error = "Failed to create project";
                include __DIR__ . '/../views/projects/create.php';
            }
        }
    }
    

    public function edit($id = null) {
        try {
            if (!$id && isset($_GET['id'])) {
                $id = $_GET['id'];
            }
            if (!$id && isset($_GET['project_identifier'])) {
                $id = $_GET['project_identifier'];
            }
            
            if (!$id) {
                require __DIR__ . '/../Views/projects/edit.php';
                return;
            }

            if (!is_numeric($id) || $id <= 0) {
                $error = "ID invalid. Te rugăm să introduci un număr valid.";
                require __DIR__ . '/../Views/projects/edit.php';
                return;
            }

            $project = $this->projectModel->getById($id);
            
            if (!$project) {
                $error = "Nu s-a găsit proiectul cu ID-ul $id.";
                require __DIR__ . '/../Views/projects/edit.php';
                return;
            }

            try {
                $projectWithDetails = $this->projectModel->getById($id);
                if ($projectWithDetails) {
                    $project = $projectWithDetails;
                }
            } catch (Exception $e) {
                // Continue with basic project data
            }

            try {
                require_once __DIR__ . '/../Models/StatusProject.php';
                require_once __DIR__ . '/../Models/User.php';
                
                $statusModel = new StatusProject();
                $userModel = new User();
                
                $statuses = $statusModel->getAll();
                $users = $userModel->getAll();
            } catch (Exception $e) {
                $statuses = [];
                $users = [];
            }

            require __DIR__ . '/../Views/projects/edit.php';
            
        } catch (Exception $e) {
            $error = "Eroare la încărcarea proiectului: " . $e->getMessage();
            require __DIR__ . '/../Views/projects/edit.php';
        }
    }

    public function update($id = null) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF validation
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die('CSRF token validation failed');
            }
            
            if (!$id && isset($_POST['id'])) {
                $id = $_POST['id'];
            }

            if (!$id) {
                $error = "ID lipsește.";
                require __DIR__ . '/../Views/projects/edit.php';
                return;
            }

            $titlu = trim($_POST['titlu'] ?? '');
            $tip = $_POST['tip'] ?? '';
            $descriere = trim($_POST['descriere_viziune'] ?? '');
            $buget = floatval($_POST['buget_aproximativ'] ?? 0);
            $durata = intval($_POST['durata_derulare'] ?? 0);
            $poster_url = trim($_POST['poster_url'] ?? '');
            $id_status = $_POST['id_status_proiect'] ?? '';
            $id_contribuitor = $_POST['id_contribuitor_principal'] ?? '';

            if (empty($titlu)) {
                $error = "Titlul proiectului este obligatoriu.";
                $project = $this->projectModel->getById($id);
                require __DIR__ . '/../Views/projects/edit.php';
                return;
            }

            if (empty($tip)) {
                $error = "Tipul proiectului este obligatoriu.";
                $project = $this->projectModel->getById($id);
                require __DIR__ . '/../Views/projects/edit.php';
                return;
            }

            try {
                $projectData = [
                    'title' => $titlu,
                    'tip' => $tip,
                    'descriere_viziune' => !empty($descriere) ? $descriere : null,
                    'buget_aproximativ' => $buget,
                    'durata_derulare' => $durata > 0 ? $durata : null,
                    'poster_url' => !empty($poster_url) ? $poster_url : null,
                    'id_status' => !empty($id_status) ? $id_status : null,
                    'contribuitor' => !empty($id_contribuitor) ? $id_contribuitor : null
                ];

                if ($this->projectModel->update($id, $projectData)) {
                    $_SESSION['success'] = "Proiect actualizat cu succes!";
                    header('Location: /views/projects');
                    exit;
                } else {
                    $error = "Eroare la actualizarea proiectului.";
                    $project = $this->projectModel->getById($id);
                    require __DIR__ . '/../Views/projects/edit.php';
                }

            } catch (Exception $e) {
                $error = "Eroare de sistem: " . $e->getMessage();
                $project = $this->projectModel->getById($id);
                require __DIR__ . '/../Views/projects/edit.php';
            }
        }
    }
    
    public function delete($id = null) {
        try {
            if (!$id && isset($_GET['id'])) {
                $id = $_GET['id'];
            }
            if (!$id && isset($_GET['project_identifier'])) {
                $id = $_GET['project_identifier'];
            }
            
            if (!$id) {
                require __DIR__ . '/../Views/projects/delete.php';
                return;
            }

            if (!is_numeric($id) || $id <= 0) {
                $error = "ID invalid. Te rugăm să introduci un număr valid.";
                require __DIR__ . '/../Views/projects/delete.php';
                return;
            }

            $project = $this->projectModel->getById($id);
            
            if (!$project) {
                $error = "Nu s-a găsit proiectul cu ID-ul $id.";
                require __DIR__ . '/../Views/projects/delete.php';
                return;
            }

            try {
                $projectWithDetails = $this->projectModel->getById($id);
                if ($projectWithDetails) {
                    $project = $projectWithDetails;
                }
            } catch (Exception $e) {
            }

            require __DIR__ . '/../Views/projects/delete.php';
            
        } catch (Exception $e) {
            $error = "Eroare la încărcarea proiectului: " . $e->getMessage();
            require __DIR__ . '/../Views/projects/delete.php';
        }
    }

    public function destroy($id = null) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF validation
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die('CSRF token validation failed');
            }
            
            if (!$id && isset($_POST['project_id'])) {
                $id = $_POST['project_id'];
            }
            if (!$id && isset($_POST['id'])) {
                $id = $_POST['id'];
            }

            if (!$id) {
                $_SESSION['error'] = "ID lipsește.";
                header('Location: /views/projects');
                exit;
            }

            $requiredCheckboxes = ['confirm_delete', 'double_confirm', 'final_confirm'];
            foreach ($requiredCheckboxes as $checkbox) {
                if (!isset($_POST[$checkbox])) {
                    $_SESSION['error'] = "Trebuie să confirmi toate casețele pentru a continua.";
                    header('Location: /projects/delete?project_identifier=' . $id);
                    exit;
                }
            }

            try {
                $project = $this->projectModel->getById($id);
                if (!$project) {
                    $_SESSION['error'] = "Proiectul nu a fost găsit.";
                    header('Location: /views/projects');
                    exit;
                }

                if ($this->projectModel->delete($id)) {
                    $_SESSION['success'] = "Proiectul '{$project['titlu']}' a fost șters cu succes!";
                } else {
                    $_SESSION['error'] = "Eroare la ștergerea proiectului.";
                }

            } catch (Exception $e) {
                $_SESSION['error'] = "Eroare de sistem: " . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = "Metodă de request invalidă.";
        }

        header('Location: /views/projects');
        exit;
    }
}