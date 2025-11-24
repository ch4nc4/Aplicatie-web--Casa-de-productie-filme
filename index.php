<?php

$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);$projDir = '/src/Views/projects/';
$viewDir = '/src/Views/';
$descDir = '/src/Views/description/';
$userDir = '/src/Views/users/';
$statDir = '/src/Views/statuses/';

switch ($request) {
    case '':
    case '/':
        require __DIR__ . $viewDir . 'index.php';
        break;

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

    default:
        http_response_code(404);
        require __DIR__ . $viewDir . '404.php';
        break;
}