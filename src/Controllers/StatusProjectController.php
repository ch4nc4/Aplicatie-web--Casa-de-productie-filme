<?php

require_once __DIR__ . '/../Models/StatusProject.php';

class StatusProjectController {
    private $statusModel;

    public function __construct() {
        $this->statusModel = new StatusProject();
        session_start();
    }

    public function index() {
        try {
            $statuses = $this->statusModel->getAllWithProjectCount();
            require __DIR__ . '/../Views/statuses/index.php';
        } catch (Exception $e) {
            $error = "Eroare la încărcarea statusurilor: " . $e->getMessage();
            require __DIR__ . '/../Views/statuses/index.php';
        }
    }

    public function create() {
        require __DIR__ . '/../Views/statuses/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF validation
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die('CSRF token validation failed');
            }
            
            $nume = trim($_POST['nume'] ?? '');
            $data_start = $_POST['data_start'] ?? '';
            $data_finalizare = $_POST['data_finalizare'] ?? '';
            $nota_aditionala = trim($_POST['nota_aditionala'] ?? '');

            if (empty($nume)) {
                $error = "Numele statusului este obligatoriu.";
                require __DIR__ . '/../Views/statuses/create.php';
                return;
            }

            if (empty($data_start)) {
                $error = "Data start este obligatorie.";
                require __DIR__ . '/../Views/statuses/create.php';
                return;
            }

            if ($data_start && $data_finalizare && strtotime($data_finalizare) <= strtotime($data_start)) {
                $error = "Data de finalizare trebuie să fie după data de start.";
                require __DIR__ . '/../Views/statuses/create.php';
                return;
            }

            try {
                $statusData = [
                    'nume' => $nume,
                    'data_start' => $data_start,
                    'data_finalizare' => !empty($data_finalizare) ? $data_finalizare : null,
                    'nota_aditionala' => !empty($nota_aditionala) ? $nota_aditionala : null
                ];

                if ($this->statusModel->create($statusData)) {
                    $_SESSION['success'] = "Status creat cu succes!";
                    header('Location: /views/statuses');
                    exit;
                } else {
                    $error = "Eroare la crearea statusului.";
                    require __DIR__ . '/../Views/statuses/create.php';
                }

            } catch (Exception $e) {
                $error = "Eroare de sistem: " . $e->getMessage();
                require __DIR__ . '/../Views/statuses/create.php';
            }
        } else {
            header('Location: /statuses/create');
            exit;
        }
    }

    public function edit($id = null) {
        try {
            if (!$id && isset($_GET['id'])) {
                $id = $_GET['id'];
            }
            
            if (!$id) {
                require __DIR__ . '/../Views/statuses/edit.php';
                return;
            }

            if (!is_numeric($id) || $id <= 0) {
                $error = "ID invalid. Te rugăm să introduci un număr valid.";
                require __DIR__ . '/../Views/statuses/edit.php';
                return;
            }

            $status = $this->statusModel->getById($id);
            
            if (!$status) {
                $error = "Nu s-a găsit statusul cu ID-ul $id.";
                require __DIR__ . '/../Views/statuses/edit.php';
                return;
            }

            try {
                $status['projects_count'] = $this->statusModel->getProjectsCount($id);
            } catch (Exception $e) {
                $status['projects_count'] = 0; // Default if method doesn't exist
            }

            require __DIR__ . '/../Views/statuses/edit.php';
            
        } catch (Exception $e) {
            $error = "Eroare la încărcarea statusului: " . $e->getMessage();
            require __DIR__ . '/../Views/statuses/edit.php';
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
                require __DIR__ . '/../Views/statuses/edit.php';
                return;
            }

            $nume = trim($_POST['nume'] ?? '');
            $data_start = $_POST['data_start'] ?? '';
            $data_finalizare = $_POST['data_finalizare'] ?? '';
            $nota_aditionala = trim($_POST['nota_aditionala'] ?? '');

            if (empty($nume)) {
                $error = "Numele statusului este obligatoriu.";
                $status = $this->statusModel->getById($id);
                require __DIR__ . '/../Views/statuses/edit.php';
                return;
            }

            try {
                $statusData = [
                    'nume' => $nume,
                    'data_start' => $data_start,
                    'data_finalizare' => !empty($data_finalizare) ? $data_finalizare : null,
                    'nota_aditionala' => !empty($nota_aditionala) ? $nota_aditionala : null
                ];

                if ($this->statusModel->update($id, $statusData)) {
                    $_SESSION['success'] = "Status actualizat cu succes!";
                    header('Location: /views/statuses');
                    exit;
                } else {
                    $error = "Eroare la actualizarea statusului.";
                    $status = $this->statusModel->getById($id);
                    require __DIR__ . '/../Views/statuses/edit.php';
                }

            } catch (Exception $e) {
                $error = "Eroare de sistem: " . $e->getMessage();
                $status = $this->statusModel->getById($id);
                require __DIR__ . '/../Views/statuses/edit.php';
            }
        }
    }

    public function delete($id = null) {
        try {
            if (!$id && isset($_GET['id'])) {
                $id = $_GET['id'];
            }
            
            if (!$id) {
                require __DIR__ . '/../Views/statuses/delete.php';
                return;
            }

            if (!is_numeric($id) || $id <= 0) {
                $error = "ID invalid. Te rugăm să introduci un număr valid.";
                require __DIR__ . '/../Views/statuses/delete.php';
                return;
            }

            $status = $this->statusModel->getById($id);
            
            if (!$status) {
                $error = "Nu s-a găsit statusul cu ID-ul $id.";
                require __DIR__ . '/../Views/statuses/delete.php';
                return;
            }

            $status['projects_count'] = 0;
            $projectCount = 0;

            require __DIR__ . '/../Views/statuses/delete.php';
            
        } catch (Exception $e) {
            $error = "Eroare la încărcarea statusului: " . $e->getMessage();
            require __DIR__ . '/../Views/statuses/delete.php';
        }
    }

    public function destroy($id = null) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF validation
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die('CSRF token validation failed');
            }
            
            if (!$id && isset($_POST['id'])) {
                $id = $_POST['id'];
            }

            if (!$id) {
                $_SESSION['error'] = "ID lipsește.";
                header('Location: /views/statuses');
                exit;
            }

            if (!isset($_POST['confirm_delete']) || !isset($_POST['double_confirm'])) {
                $_SESSION['error'] = "Trebuie să confirmi ambele casete pentru a continua.";
                header('Location: /statuses/delete?id=' . $id);
                exit;
            }

            try {
                $status = $this->statusModel->getById($id);
                if (!$status) {
                    $_SESSION['error'] = "Statusul nu a fost găsit.";
                    header('Location: /views/statuses');
                    exit;
                }

                try {
                    $projectCount = $this->statusModel->getProjectsCount($id);
                    if ($projectCount > 0) {
                        $_SESSION['error'] = "Nu se poate șterge statusul. Există {$projectCount} proiect(e) care folosesc acest status.";
                        header('Location: /views/statuses');
                        exit;
                    }
                } catch (Exception $e) {
                    }

                if ($this->statusModel->delete($id)) {
                    $_SESSION['success'] = "Statusul '{$status['nume']}' a fost șters cu succes!";
                } else {
                    $_SESSION['error'] = "Eroare la ștergerea statusului.";
                }

            } catch (Exception $e) {
                $_SESSION['error'] = "Eroare de sistem: " . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = "Metodă de request invalidă.";
        }

        header('Location: /views/statuses');
        exit;
    }

    public function getAll() {
        try {
            $statuses = $this->statusModel->getAll();
            header('Content-Type: application/json');
            echo json_encode($statuses);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getById($id) {
        try {
            $status = $this->statusModel->getById($id);
            header('Content-Type: application/json');
            
            if ($status) {
                echo json_encode($status);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Status not found']);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    private function checkAuth() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: /login');
            exit;
        }
    }
}