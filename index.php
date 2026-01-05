<?php
session_start();

header('X-Frame-Options: SAMEORIGIN');
define('CSRF_TOKEN_NAME', 'csrf_token');
if (empty($_SESSION[CSRF_TOKEN_NAME])) {
    $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
}
function csrf_token_field() {
    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . htmlspecialchars($_SESSION[CSRF_TOKEN_NAME]) . '">';
}

// **INCLUDE MODELS ȘI SERVICES PENTRU EMAIL:**
require_once __DIR__ . '/src/Models/User.php';
require_once __DIR__ . '/src/Models/Project.php';
require_once __DIR__ . '/src/Models/EmailMessage.php';
require_once __DIR__ . '/src/Services/EmailService.php';
require_once __DIR__ . '/src/Controllers/EmailController.php';

// Database connection (dacă ai un fișier de configurare DB)
if (file_exists(__DIR__ . '/config/database.php')) {
    require_once __DIR__ . '/config/database.php';
}

// Load .env variables
if (!isset($_ENV['DB_HOST']) && file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && !str_starts_with(trim($line), '#')) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value, '"');
        }
    }
}

use App\Controllers\EmailController;

$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

$projDir = '/src/Views/projects/';
$viewDir = '/src/Views/';
$descDir = '/src/Views/description/';
$userDir = '/src/Views/users/';
$statDir = '/src/Views/statuses/';

// **ÎNDEPĂRTEAZĂ ACESTE LINII VECHI:**
// $emailRouteHandled = include __DIR__ . '/email_routes.php';
// if ($emailRouteHandled) {
//     exit;
// }

switch ($request) {
    case '':
    case '/':
        require __DIR__ . $viewDir . 'index.php';
        break;

    // **ADAUGĂ RUTELE DE EMAIL AICI:**
    
    // Email routes
    case '/messages/inbox':
        try {
            $emailController = new EmailController();
            $emailController->showInbox();
        } catch (Exception $e) {
            error_log("Eroare la inbox: " . $e->getMessage());
            http_response_code(500);
            echo "Eroare la încărcarea inbox-ului";
        }
        break;
        
    case '/messages/compose':
        try {
            $emailController = new EmailController();
            $emailController->showCompose();
        } catch (Exception $e) {
            error_log("Eroare la compose: " . $e->getMessage());
            http_response_code(500);
            echo "Eroare la încărcarea formularului de compunere";
        }
        break;
        
    case '/messages/sent':
        try {
            $emailController = new EmailController();
            $emailController->getSentMessages();
        } catch (Exception $e) {
            error_log("Eroare la sent messages: " . $e->getMessage());
            http_response_code(500);
            echo "Eroare la încărcarea mesajelor trimise";
        }
        break;

    case '/messages/send':
        if ($requestMethod === 'POST') {
            try {
                $emailController = new EmailController();
                $emailController->sendMessage();
            } catch (Exception $e) {
                error_log("Eroare la trimiterea mesajului: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Eroare la trimiterea mesajului']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case '/messages/mark-all-read':
        if ($requestMethod === 'POST') {
            try {
                $emailController = new EmailController();
                $emailController->markAllAsRead();
            } catch (Exception $e) {
                error_log("Eroare la marcarea ca citite: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Eroare la marcarea mesajelor']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case '/messages/view':
        try {
            $emailController = new EmailController();
            $emailController->viewMessage();
        } catch (Exception $e) {
            error_log("Eroare la vizualizarea mesajului: " . $e->getMessage());
            http_response_code(500);
            echo "Eroare la încărcarea mesajului";
        }
        break;

    case '/interactions/role-update':
        try {
            $emailController = new EmailController();
            $emailController->showRoleChangeForm();
        } catch (Exception $e) {
            error_log("Eroare la role update form: " . $e->getMessage());
            http_response_code(500);
            echo "Eroare la încărcarea formularului";
        }
        break;

    case '/interactions/send-role-change':
        if ($requestMethod === 'POST') {
            try {
                $emailController = new EmailController();
                $emailController->sendRoleChangeRequest();
            } catch (Exception $e) {
                error_log("Eroare la cererea de schimbare rol: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Eroare la trimiterea cererii']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case '/interactions/production-staff':
        try {
            $emailController = new EmailController();
            $emailController->showStaffMessaging();
        } catch (Exception $e) {
            error_log("Eroare la staff messaging: " . $e->getMessage());
            http_response_code(500);
            echo "Eroare la încărcarea paginii staff";
        }
        break;

    case '/interactions/send-staff-message':
        if ($requestMethod === 'POST') {
            try {
                $emailController = new EmailController();
                $emailController->sendStaffMessage();
            } catch (Exception $e) {
                error_log("Eroare la mesajul către staff: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Eroare la trimiterea mesajului']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    // **RUTELE TALE EXISTENTE CONTINUĂ AICI:**

    case '/views/projects':
        require __DIR__ . $projDir . 'index.php';
        break;

    case '/views/descriptions':
        require __DIR__ . $descDir . 'index.php';
        break;

    case '/views/users':
    case '/login':
        require __DIR__ . $userDir . 'login.php';
        break;

    case '/signup':
        require __DIR__ . $userDir . 'signup.php';
        break;

    case '/auth/login':
        require_once __DIR__ . '/src/Controllers/AuthController.php';
        $controller = new AuthController();
        $controller->login();
        break;

    case '/auth/signup':
        require_once __DIR__ . '/src/Controllers/AuthController.php';
        $controller = new AuthController();
        $controller->signup();
        break;

    case '/logout':
        require_once __DIR__ . '/src/Controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;

    case '/user/profile/update':
        require_once __DIR__ . '/src/Controllers/UserController.php';
        $controller = new UserController();
        $controller->updateProfile();
        break;

    case '/user/profile':
        require_once __DIR__ . '/src/Controllers/UserController.php';
        $controller = new UserController();
        $controller->profile();
        break;

    case '/views/statuses':
        require __DIR__ . $statDir . 'index.php';
        break;

     case '/statuses/create':
        require __DIR__ . $statDir . 'create.php';
        break;

    case '/statuses/show':
        try {
            require_once __DIR__ . '/src/Models/StatusProject.php';
            $statusModel = new StatusProject();
            
            $statuses = $statusModel->getAll();            
            require __DIR__ . $statDir . 'show.php';
        } catch (Exception $e) {
            echo "Error at step: " . $e->getMessage() . "<br>";
            echo "Error trace: " . $e->getTraceAsString();
        }
        break;

    case '/statuses/store':  
        require_once __DIR__ . '/src/Controllers/StatusProjectController.php';
        $controller = new StatusProjectController();
        $controller->store();
        break;

    case '/statuses/edit':
        require_once __DIR__ . '/src/Controllers/StatusProjectController.php';
        $controller = new StatusProjectController();
        $controller->edit();
        break;

    case '/statuses/update':
        require_once __DIR__ . '/src/Controllers/StatusProjectController.php';
        $controller = new StatusProjectController();
        $controller->update();
        break;

    case '/statuses/delete':
        require_once __DIR__ . '/src/Controllers/StatusProjectController.php';
        $controller = new StatusProjectController();
        $controller->delete();
        break;

    case '/statuses/destroy':
        require_once __DIR__ . '/src/Controllers/StatusProjectController.php';
        $controller = new StatusProjectController();
        $controller->destroy();
        break;

    // ... restul rutelor tale rămân la fel ...

    case '/projects/create':
        require __DIR__ . $projDir . 'create.php';
        break;

    case '/projects/show':
        try {
            require_once __DIR__ . '/src/Models/Project.php';
            $projectModel = new Project();
            
            $projects = $projectModel->getAll();            
            require __DIR__ . $projDir . 'show.php';
        } catch (Exception $e) {
            echo "Error at step: " . $e->getMessage() . "<br>";
            echo "Error trace: " . $e->getTraceAsString();
        }
        break;

    case '/projects/edit':
        require_once __DIR__ . '/src/Controllers/ProjectController.php';
        $controller = new ProjectController();
        $controller->edit();
        break;

    case '/projects/update':
        require_once __DIR__ . '/src/Controllers/ProjectController.php';
        $controller = new ProjectController();
        $controller->update();
        break;

    case '/projects/delete':
        require_once __DIR__ . '/src/Controllers/ProjectController.php';
        $controller = new ProjectController();
        $controller->delete();
        break;

    case '/projects/destroy':
        require_once __DIR__ . '/src/Controllers/ProjectController.php';
        $controller = new ProjectController();
        $controller->destroy();
        break;

    case '/projects/store':  
        require_once __DIR__ . '/src/Controllers/ProjectController.php';
        $controller = new ProjectController();
        $controller->store();
        break;

    case '/views/users':
        require __DIR__ . $userDir . 'index.php';
        break;

    case '/admin/users':
        require_once __DIR__ . '/src/Controllers/AdminUserController.php';
        $controller = new AdminUserController();
        $controller->index();
        break;

    case '/admin/user/add-role':
        require_once __DIR__ . '/src/Controllers/AdminUserController.php';
        $controller = new AdminUserController();
        $controller->addRole($_POST['user_id'] ?? null);
        break;

    case '/admin/user/remove-role':
        require_once __DIR__ . '/src/Controllers/AdminUserController.php';
        $controller = new AdminUserController();
        $controller->removeRole($_POST['role_user_id'] ?? null);
        break;

    case '/admin/user/add-project':
        require_once __DIR__ . '/src/Controllers/AdminUserController.php';
        $controller = new AdminUserController();
        $controller->addProjectMembership($_POST['user_id'] ?? null);
        break;

    case '/admin/user/remove-project':
        require_once __DIR__ . '/src/Controllers/AdminUserController.php';
        $controller = new AdminUserController();
        $controller->removeProjectMembership($_POST['project_member_id'] ?? null);
        break;

    case '/roles/store':  
        require_once __DIR__ . '/src/Controllers/RoleController.php';
        $controller = new RoleController();
        $controller->store();
        break;

    case '/roles/create':
        require __DIR__ . '/src/Views/roles/create.php';
        break;

    case '/admin/roles':
        require_once __DIR__ . '/src/Controllers/RoleController.php';
        $controller = new RoleController();
        $controller->index(); // shows all roles
        break;

    case '/admin/roles/delete':
        require_once __DIR__ . '/src/Controllers/RoleController.php';
        $controller = new RoleController();
        $controller->deleteRole(); 
        break;

    case '/admin/roles/create':
        require_once __DIR__ . '/src/Controllers/RoleController.php';
        $controller = new RoleController();
        $controller->createRole(); 
        break;

    case '/views/watchlist':
        require __DIR__ . '/src/Views/watchlist/index.php';
        break;

    case '/watchlist/show':
        try {
            session_start();
            require_once __DIR__ . '/src/Models/WatchlistItemModel.php';
            $watchlistModel = new WatchlistItem();
            
            $userId = $_SESSION['user_id'] ?? null;
            if (!$userId) {
                header('Location: /login');
                exit;
            }
            $watchlistItems = $watchlistModel->getAllByUser($userId); 
            require __DIR__ . '/src/Views/watchlist/show.php'; 
        
        } catch (Exception $e) {
            echo "Error at step: " . $e->getMessage() . "<br>";
            echo "Error trace: " . $e->getTraceAsString();
        }
        break;

    case '/watchlist/create':
        require_once __DIR__ . '/src/Views/watchlist/create.php';
        break;

    case '/watchlist/add':
        require_once __DIR__ . '/src/Controllers/WatchlistController.php';
        $controller = new WatchlistController();
        $controller->add();
        break;  

    case '/watchlist/delete':
        require_once __DIR__ . '/src/Controllers/WatchlistController.php';
        $controller = new WatchlistController();
        $controller->remove();
        break;
    
    case '/watchlist':
        require_once __DIR__ . '/src/Controllers/WatchlistController.php';
        $controller = new WatchlistController();
        $userId = $_GET['user_id'] ?? null;
        if ($userId) {
            $controller->index($userId);
        } else {
            echo "User ID is required for watchlist.";
        }
        break;
    
    case '/auth/set-password':
        require_once __DIR__ . '/src/Controllers/AuthController.php';
        $controller = new AuthController();
        $controller->setPassword();
        break;

    case '/crew':
        require_once __DIR__ . '/src/Controllers/CrewController.php';
        $controller = new CrewController();
        $controller->index();
        break;

    case '/top-movies':
        require_once __DIR__ . '/src/Controllers/TopMoviesController.php';
        $controller = new TopMoviesController();
        $controller->index();
        break;

    case '/top-movies/refresh':
        require_once __DIR__ . '/src/Controllers/TopMoviesController.php';
        $controller = new TopMoviesController();
        $controller->refresh();
         break;

    // Statistici (doar pentru admini)
    case '/statistics':
    case '/dashboard':
    case '/admin/statistics':
        require_once __DIR__ . '/src/Views/statistics/index.php';
        break;

    default:
        // **Verifică și rute cu parametri pentru mesaje:**
        if (preg_match('/^\/messages\/mark-read\/(\d+)$/', $request, $matches)) {
            $_GET['messageId'] = $matches[1];
            if ($requestMethod === 'POST') {
                try {
                    $emailController = new EmailController();
                    $emailController->markAsRead();
                } catch (Exception $e) {
                    error_log("Eroare la marcarea ca citit: " . $e->getMessage());
                    echo json_encode(['success' => false, 'message' => 'Eroare']);
                }
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
            }
             } elseif (preg_match('/^\/messages\/view\/(\d+)$/', $request, $matches)) {
            $_GET['messageId'] = $matches[1];
            try {
                $emailController = new EmailController();
                $emailController->viewMessage();
            } catch (Exception $e) {
                error_log("Eroare la vizualizarea mesajului: " . $e->getMessage());
                http_response_code(500);
                echo "Eroare la încărcarea mesajului";
            }
        } else {
            // 404 pentru rute necunoscute
            http_response_code(404);
            if (file_exists(__DIR__ . $viewDir . '404.php')) {
                require __DIR__ . $viewDir . '404.php';
            } else {
                echo '<h1>404 - Pagina nu a fost găsită</h1>';
            }
        }
        break;
}