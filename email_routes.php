<?php
// Email routing configuration

// Models
require_once __DIR__ . '/src/Models/User.php';
require_once __DIR__ . '/src/Models/Project.php';
require_once __DIR__ . '/src/Models/EmailMessage.php';

// Services
require_once __DIR__ . '/src/Services/EmailService.php';

// Controllers
require_once __DIR__ . '/src/Controllers/EmailController.php';

// Database connection (dacă ai un fișier de configurare DB)
if (file_exists(__DIR__ . '/config/database.php')) {
    require_once __DIR__ . '/config/database.php';
}

// Load .env variables aici dacă nu sunt încărcate în index.php
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

// Parse the current URL
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];
$path = parse_url($requestUri, PHP_URL_PATH);

// Verifică dacă e rută de email
$emailRoutes = [
    '/messages/inbox',
    '/messages/compose', 
    '/messages/sent',
    '/messages/send',
    '/messages/mark-all-read',
    '/messages/mark-read',
    '/messages/view',
    '/interactions/role-update',
    '/interactions/send-role-change',
    '/interactions/production-staff',
    '/interactions/send-staff-message'
];

try {
    $emailController = new EmailController();
} catch (Error $e) {
    error_log("Eroare la inițializarea EmailController: " . $e->getMessage());
    return false;
} catch (Exception $e) {
    error_log("Excepție la inițializarea EmailController: " . $e->getMessage());
    return false;
}

// Email messaging routes
switch($path) {
    // Inbox and messaging views
    case '/messages/inbox':
        $emailController->showInbox();
        break;
        
    case '/messages/compose':
        $emailController->showCompose();
        break;
        
    case '/messages/sent':
        $emailController->getSentMessages();
        break;

    // Message actions
    case '/messages/send':
        if ($requestMethod === 'POST') {
            $emailController->sendMessage();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case '/messages/mark-all-read':
        if ($requestMethod === 'POST') {
            $emailController->markAllAsRead();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    // Role change routes
    case '/interactions/role-update':
        $emailController->showRoleChangeForm();
        break;

    case '/interactions/send-role-change':
        if ($requestMethod === 'POST') {
            $emailController->sendRoleChangeRequest();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    case '/interactions/production-staff':
        $emailController->showStaffMessaging();
        break;

    case '/interactions/send-staff-message':
        if ($requestMethod === 'POST') {
            $emailController->sendStaffMessage();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    default:
        // Handle dynamic routes with parameters
        if (preg_match('/^\/messages\/mark-read\/(\d+)$/', $path, $matches)) {
            $_GET['messageId'] = $matches[1];
            if ($requestMethod === 'POST') {
                $emailController->markAsRead();
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
            }
        } elseif (preg_match('/^\/messages\/view\/(\d+)$/', $path, $matches)) {
            $_GET['messageId'] = $matches[1];
            $emailController->viewMessage();
        } else {
            // Route not found - return false to let main router handle it
            return false;
        }
        break;
}

return true; // Route was handled
?>