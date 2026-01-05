<?php

namespace App\Controllers;

use App\Services\EmailService;
require_once __DIR__ . '/../Models/User.php';

require_once __DIR__ . '/../Models/Project.php';

require_once __DIR__ . '/../Models/EmailMessage.php';

class EmailController {
    private $emailService;
    private $userModel;
    private $projectModel;
    private $emailMessageModel;  

    public function __construct() {
        $this->emailService = new EmailService();
        $this->userModel = new \User();
        $this->projectModel = new \Project();
        $this->emailMessageModel = new \EmailMessage();  
    }

    public function showRoleChangeForm() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        require_once __DIR__ . '/../Views/interactions/role_update.php';
    }

    public function sendRoleChangeRequest() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Nu ești autentificat']);
            return;
        }

        $requestedRole = $_POST['requested_role'] ?? '';
        $reason = $_POST['reason'] ?? '';

        if (empty($requestedRole) || empty($reason)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Toate câmpurile sunt obligatorii']);
            return;
        }

        $user = $this->userModel->findById($_SESSION['user_id']);
        
        $success = $this->emailService->sendRoleChangeRequest(
            $user['email'],
            $user['name'],
            $user['role'],
            $requestedRole,
            $reason
        );

        if ($success) {
            $_SESSION['success'] = 'Cererea de schimbare rol a fost trimisă cu succes!';
            echo json_encode(['success' => true, 'message' => 'Email trimis cu succes']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Eroare la trimiterea email-ului']);
        }
    }

    public function showProjectMessageForm() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $user = $this->userModel->findById($_SESSION['user_id']);
        
        if ($user['role'] !== 'lider_productie') {
            header('Location: /dashboard');
            exit;
        }

        // Obține proiectele utilizatorului
        $projects = $this->projectModel->getProjectsByLeader($_SESSION['user_id']);
        
        // Obține alți lideri de producție
        $leaders = $this->userModel->getByRole('lider_productie');
        $leaders = array_filter($leaders, function($leader) {
            return $leader['id'] != $_SESSION['user_id']; // Exclude utilizatorul curent
        });

        require_once __DIR__ . '/../Views/interactions/production_leaders.php';
    }

    public function sendProjectMessage() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Nu ești autentificat']);
            return;
        }

        $user = $this->userModel->findById($_SESSION['user_id']);
        
        if ($user['role'] !== 'lider_productie') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Nu ai permisiunea necesară']);
            return;
        }

        $toUserId = $_POST['to_user_id'] ?? '';
        $projectId = $_POST['project_id'] ?? '';
        $message = $_POST['message'] ?? '';

        if (empty($toUserId) || empty($projectId) || empty($message)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Toate câmpurile sunt obligatorii']);
            return;
        }

        $toUser = $this->userModel->findById($toUserId);
        $project = $this->projectModel->findById($projectId);

        if (!$toUser || !$project) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Utilizator sau proiect nu a fost găsit']);
            return;
        }

        $success = $this->emailService->sendProjectMessage(
            $user,
            $toUser,
            $project['title'],
            $message
        );

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Mesajul a fost trimis cu succes']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Eroare la trimiterea mesajului']);
        }
    }

    public function showStaffMessageForm() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $user = $this->userModel->findById($_SESSION['user_id']);
        
        if ($user['role'] !== 'staff_productie') {
            header('Location: /dashboard');
            exit;
        }

        // Obține liderii de producție
        $leaders = $this->userModel->getByRole('lider_productie');

        require_once __DIR__ . '/../Views/interactions/production_staff.php';
    }

    public function sendStaffMessage() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Nu ești autentificat']);
            return;
        }

        $user = $this->userModel->findById($_SESSION['user_id']);
        
        if ($user['role'] !== 'staff_productie') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Nu ai permisiunea necesară']);
            return;
        }

        $toUserId = $_POST['to_user_id'] ?? '';
        $subject = $_POST['subject'] ?? '';
        $message = $_POST['message'] ?? '';

        if (empty($toUserId) || empty($subject) || empty($message)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Toate câmpurile sunt obligatorii']);
            return;
        }

        $leader = $this->userModel->findById($toUserId);

        if (!$leader || $leader['role'] !== 'lider_productie') {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Liderul de producție nu a fost găsit']);
            return;
        }

        $success = $this->emailService->sendStaffToLeaderMessage(
            $user,
            $leader,
            $subject,
            $message
        );

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Mesajul a fost trimis cu succes']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Eroare la trimiterea mesajului']);
        }
    }

    // NEW METHODS FOR EMAIL MESSAGING SYSTEM

    public function showInbox() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        require_once __DIR__ . '/../Views/messages/inbox.php';
    }

    public function showCompose() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Obține utilizatorul curent
        $currentUser = $this->userModel->findById($_SESSION['user_id']);
        if (!$currentUser) {
            header('Location: /login');
            exit;
        }

        // Obține toți utilizatorii pentru dropdown (exclude utilizatorul curent)
        try {
            $users = $this->userModel->getAll();
            $users = array_filter($users, function($user) {
                return $user['id'] != $_SESSION['user_id'];
            });
        } catch (Exception $e) {
            error_log("Eroare la obținerea utilizatorilor: " . $e->getMessage());
            $users = [];
        }

        // Obține toate proiectele pentru dropdown
        try {
            $projects = $this->projectModel->getAll();
        } catch (Exception $e) {
            error_log("Eroare la obținerea proiectelor: " . $e->getMessage());
            $projects = [];
        }

        // Verifică dacă este răspuns la un mesaj
        $replyToMessage = null;
        if (isset($_GET['reply_to']) && is_numeric($_GET['reply_to'])) {
            try {
                $replyToMessage = $this->emailMessageModel->getMessage($_GET['reply_to'], $_SESSION['user_id']);
            } catch (Exception $e) {
                error_log("Eroare la obținerea mesajului pentru răspuns: " . $e->getMessage());
            }
        }

        // Setează variabilele pentru view
        $data = [
            'users' => $users,
            'projects' => $projects,
            'currentUser' => $currentUser,
            'replyToMessage' => $replyToMessage
        ];

        // Pentru a face variabilele disponibile în view
        extract($data);

        // Include view-ul
        require_once __DIR__ . '/../Views/messages/compose.php';
    }

    public function sendMessage() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Nu ești autentificat']);
            return;
        }

        $fromUserId = $_SESSION['user_id'];
        $toUserId = $_POST['to_user_id'] ?? '';
        $subject = $_POST['subject'] ?? '';
        $message = $_POST['message'] ?? '';
        $type = $_POST['type'] ?? 'general';
        $projectId = $_POST['project_id'] ?? null;
        $replyToId = $_POST['reply_to_id'] ?? null;

        // Validation
        if (empty($toUserId) || empty($subject) || empty($message)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Toate câmpurile sunt obligatorii']);
            return;
        }

        // Get users
        $fromUser = $this->userModel->findById($fromUserId);
        $toUser = $this->userModel->findById($toUserId);

        if (!$fromUser || !$toUser) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Utilizatori invalid']);
            return;
        }

        // Handle different message types
        $success = false;
        switch($type) {
            case 'role_change':
                if ($toUser['rol'] !== 'admin') {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Cererile de schimbare rol pot fi trimise doar către admin']);
                    return;
                }
                $success = $this->emailService->sendRoleChangeRequest(
                    $fromUser['email'], 
                    $fromUser['prenume'] . ' ' . $fromUser['nume_familie'], 
                    $fromUser['rol'], 
                    $message, // This should be the requested role, but we'll use message for simplicity
                    $subject
                );
                break;

            case 'project':
                $projectTitle = null;
                if ($projectId) {
                    $project = $this->projectModel->getById($projectId);
                    $projectTitle = $project ? $project['title'] : null;
                }
                $success = $this->emailService->sendProjectMessage($fromUser, $toUser, $projectTitle ?? 'General', $message);
                break;

            case 'staff_message':
                if ($toUser['rol'] !== 'lider_productie' && $toUser['rol'] !== 'admin') {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Mesajele staff pot fi trimise doar către lideri sau admin']);
                    return;
                }
                $success = $this->emailService->sendStaffToLeaderMessage($fromUser, $toUser, $subject, $message);
                break;

            default:
                $success = $this->emailService->sendGeneralMessage($fromUser, $toUser, $subject, $message, $projectId);
                break;
        }

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Mesajul a fost trimis cu succes']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Eroare la trimiterea mesajului']);
        }
    }

    public function markAsRead() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Nu ești autentificat']);
            return;
        }

        $messageId = $_GET['messageId'] ?? $_POST['messageId'] ?? null;
        
        if (!$messageId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID mesaj lipsă']);
            return;
        }

        $success = $this->emailMessageModel->markAsRead($messageId, $_SESSION['user_id']);

        if ($success) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Eroare la marcarea mesajului']);
        }
    }

    public function markAllAsRead() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Nu ești autentificat']);
            return;
        }

        $success = $this->emailMessageModel->markAllAsRead($_SESSION['user_id']);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Toate mesajele au fost marcate ca citite']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Eroare la marcarea mesajelor']);
        }
    }

    public function viewMessage() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Nu ești autentificat']);
            return;
        }

        $messageId = $_GET['messageId'] ?? null;
        
        if (!$messageId) {
            http_response_code(400);
            echo 'ID mesaj lipsă';
            return;
        }

        $message = $this->emailMessageModel->getMessage($messageId, $_SESSION['user_id']);

        if (!$message) {
            http_response_code(404);
            echo 'Mesajul nu a fost găsit';
            return;
        }

        // Mark as read if it's the recipient viewing
        if ($message['to_user_id'] == $_SESSION['user_id'] && !$message['is_read']) {
            $this->emailMessageModel->markAsRead($messageId, $_SESSION['user_id']);
        }

        // Return HTML content for modal
        echo $this->renderMessageView($message);
    }

    private function renderMessageView($message) {
        $html = '<div class="message-view">';
        $html .= '<div class="message-header mb-3">';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-6">';
        $html .= '<strong>De la:</strong> ' . htmlspecialchars($message['sender_name']) . '<br>';
        $html .= '<small class="text-muted">' . htmlspecialchars($message['sender_email']) . '</small>';
        $html .= '</div>';
        $html .= '<div class="col-md-6 text-end">';
        $html .= '<strong>Data:</strong> ' . date('d.m.Y H:i', strtotime($message['sent_at'])) . '<br>';
        
        if ($message['project_name']) {
            $html .= '<small class="text-info"><i class="fas fa-project-diagram me-1"></i>' . htmlspecialchars($message['project_name']) . '</small>';
        }
        
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<hr>';
        $html .= '<h5>' . htmlspecialchars($message['subject']) . '</h5>';
        $html .= '</div>';
        
        $html .= '<div class="message-content">';
        $html .= '<div class="card bg-light">';
        $html .= '<div class="card-body">';
        $html .= nl2br(htmlspecialchars($message['message']));
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '</div>';
        
        return $html;
    }

    public function getSentMessages() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $messages = $this->emailMessageModel->getSentMessages($_SESSION['user_id']);
        
        // You could create a separate view for sent messages or reuse inbox with different data
        require_once __DIR__ . '/../Views/messages/sent.php';
    }
}