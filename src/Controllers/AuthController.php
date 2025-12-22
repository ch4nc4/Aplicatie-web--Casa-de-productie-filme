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
            // CSRF validation
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die('CSRF token validation failed');
            }

            if (!$this->validateRecaptcha()) {
                $error = "Validarea reCAPTCHA a eșuat. Te rugăm să încerci din nou.";
                require __DIR__ . '/../Views/users/login.php';
                return;
            }
            
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
                        header('Location: /');
                        exit;
                    } else {
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
            // CSRF validation
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die('CSRF token validation failed');
            }

            if (!$this->validateRecaptcha()) {
                $error = "Validarea reCAPTCHA a eșuat. Te rugăm să încerci din nou.";
                require __DIR__ . '/../Views/users/signup.php';
                return;
            }
            
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
                    // successfull signup, auto-login
                    $user = $this->userModel->getByEmail($email);
                    $this->assignAuthenticatedRole($user['id']); 

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

    private function assignAuthenticatedRole($userId) {
        require_once __DIR__ . '/../Models/RoleUser.php';
        require_once __DIR__ . '/../Models/Role.php';
        $roleUserModel = new RoleUser();
        $roleModel = new Role();

        error_log("assignAuthenticatedRole: userId = $userId");

        // Caută id-ul rolului "Utilizator autentificat"
        $role = $roleModel->getByName('Utilizator autentificat');
        if (!$role) {
            error_log("assignAuthenticatedRole: Rolul 'Utilizator autentificat' nu exista, il creez...");
            $roleModel->create([
                'nume' => 'Utilizator autentificat',
                'descriere' => 'Rol implicit pentru utilizatori autentificați'
            ]);
            $role = $roleModel->getByName('Utilizator autentificat');
        }
        $roleId = $role['id'];
        error_log("assignAuthenticatedRole: roleId = $roleId");

        // Verifică dacă userul are deja rolul
        if (!$roleUserModel->exists($userId, $roleId)) {
            error_log("assignAuthenticatedRole: user $userId NU are rolul $roleId, il atribui...");
            $data = [
                'id_user' => $userId,
                'id_rol' => $roleId,
                'assigned_at' => date('Y-m-d H:i:s'),
                'expires_at' => null
            ];
            $roleUserModel->create($data);
            error_log("assignAuthenticatedRole: rolul a fost atribuit.");
        } else {
            error_log("assignAuthenticatedRole: user $userId ARE DEJA rolul $roleId.");
        }
    }

    public function setPassword() {
        // Presupunem că user-ul este identificat prin email sau id în sesiune sau POST
        $userId = $_SESSION['user_id'] ?? null;
        $email = $_SESSION['email'] ?? ($_POST['email'] ?? null);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF validation
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die('CSRF token validation failed');
            }
            
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($newPassword) || empty($confirmPassword)) {
                $error = "Toate câmpurile sunt obligatorii.";
                require __DIR__ . '/../Views/users/login.php';
                return;
            }

            if ($newPassword !== $confirmPassword) {
                $error = "Parolele nu coincid.";
                require __DIR__ . '/../Views/users/login.php';
                return;
            }

            if (strlen($newPassword) < 6) {
                $error = "Parola trebuie să aibă cel puțin 6 caractere.";
                require __DIR__ . '/../Views/users/login.php';
                return;
            }

            // Caută user-ul după id sau email
            require_once __DIR__ . '/../Models/User.php';
            $userModel = new User();
            $user = null;
            if ($userId) {
                $user = $userModel->findById($userId);
            } elseif ($email) {
                $user = $userModel->findByEmail($email);
            }

            if (!$user) {
                $error = "Utilizatorul nu a fost găsit.";
                require __DIR__ . '/../Views/users/login.php';
                return;
            }

            // Setează parola nouă
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            if ($userModel->updatePassword($user['id'], $hashedPassword)) {
                $_SESSION['success'] = "Parola a fost setată cu succes. Te poți autentifica!";
                header('Location: /login');
                exit;
            } else {
                $error = "Eroare la setarea parolei.";
                require __DIR__ . '/../Views/users/login.php';
            }
        } else {
            header('Location: /login');
            exit;
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

         // Adaugă rolurile userului în sesiune
        require_once __DIR__ . '/../Models/RoleUser.php';
        $roleUserModel = new RoleUser();
        $roles = $roleUserModel->getByUserId($user['id']); 
        
        // Extrage doar numele rolurilor într-un array simplu
        $_SESSION['roles'] = array_map(function($role) {
            return $role['nume'];
        }, $roles);
    }

    protected function validateRecaptcha(): bool {
        $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
        if (empty($recaptchaResponse)) {
            return false;
        }

        $secret = $_ENV['RECAPTCHA_SECRET_KEY'] ?? '';
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $secret,
            'response' => $recaptchaResponse,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ];

        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            ]
        ];

        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === false) {
            return false;
        }

        $resultJson = json_decode($result);
        return $resultJson->success;
    }
}