<?php

require_once __DIR__ . '/../Models/User.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
        session_start();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $error = "Toate câmpurile sunt obligatorii.";
                require __DIR__ . '/../Views/users/login.php';
                return;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Format email invalid.";
                require __DIR__ . '/../Views/users/login.php';
                return;
            }

            try {
                $user = $this->userModel->getByEmail($email);

                if (!$user) {
                    $showSignupOption = true;
                    $error = "Nu există cont cu acest email.";
                    require __DIR__ . '/../Views/users/login.php';
                    return;
                }

                if (!empty($user['deleted_at'])) {
                    $error = "Contul este inactiv. Contactează administratorul.";
                    require __DIR__ . '/../Views/users/login.php';
                    return;
                }

                if (!empty($user['hash_parola'])) {
                    if (password_verify($password, $user['hash_parola'])) {
                        // Successful login
                        $this->createSession($user);
                        header('Location: /views/projects');
                        exit;
                    } else {
                        // $this->incrementFailedAttempts($user['id']);
                        $error = "Parolă incorectă.";
                        require __DIR__ . '/../Views/users/login.php';
                        return;
                    }
                } else {
                    $needsPasswordSetup = true;
                    $userId = $user['id'];
                    require __DIR__ . '/../Views/users/login.php';
                    return;
                }

            } catch (Exception $e) {
                $error = "Eroare de sistem: " . $e->getMessage();
                require __DIR__ . '/../Views/users/login.php';
            }
        } else {
            require __DIR__ . '/../Views/users/login.php';
        }
    }

    public function signup() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $prenume = trim($_POST['prenume'] ?? '');
            $nume_familie = trim($_POST['nume_familie'] ?? '');

            if (empty($email) || empty($password) || empty($prenume) || empty($nume_familie)) {
                $error = "Toate câmpurile sunt obligatorii.";
                require __DIR__ . '/../Views/users/signup.php';
                return;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Format email invalid.";
                require __DIR__ . '/../Views/users/signup.php';
                return;
            }

            if (strlen($password) < 6) {
                $error = "Parola trebuie să aibă cel puțin 6 caractere.";
                require __DIR__ . '/../Views/users/signup.php';
                return;
            }
            else {
                $needsPasswordSetup = false;
            }

            try {
                if ($this->userModel->emailExists($email)) {
                    $error = "Există deja un cont cu acest email.";
                    $showLoginOption = true;
                    require __DIR__ . '/../Views/users/signup.php';
                    return;
                }

                $userData = [
                    'email' => $email,
                    'hash_parola' => password_hash($password, PASSWORD_DEFAULT),
                    'prenume' => $prenume,
                    'nume_familie' => $nume_familie
                ];

                if ($this->userModel->create($userData)) {
                    $user = $this->userModel->getByEmail($email);
                    $this->createSession($user);
                    header('Location: /views/projects');
                    exit;
                } else {
                    $error = "Eroare la crearea contului.";
                    require __DIR__ . '/../Views/users/signup.php';
                }

            } catch (Exception $e) {
                $error = "Eroare de sistem: " . $e->getMessage();
                require __DIR__ . '/../Views/users/signup.php';
            }
        } else {
            require __DIR__ . '/../Views/users/signup.php';
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        header('Location: /');
        exit;
    }

    private function createSession($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['prenume'] . ' ' . $user['nume_familie'];
        $_SESSION['logged_in'] = true;
    }
}