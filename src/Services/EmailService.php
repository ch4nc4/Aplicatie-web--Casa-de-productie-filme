<?php

namespace App\Services;

require_once __DIR__ . '/../../vendor/autoload.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../Models/EmailMessage.php';

class EmailService {
    private $smtpHost;
    private $smtpPort;
    private $smtpUsername;
    private $smtpPassword;
    private $fromEmail;
    private $fromName;
    private $emailMessageModel;

    public function __construct() {
        // Configurări email
        $this->smtpHost = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com';
        $this->smtpPort = $_ENV['SMTP_PORT'] ?? 587;
        $this->smtpUsername = $_ENV['SMTP_USERNAME'] ?? '';
        $this->smtpPassword = $_ENV['SMTP_PASSWORD'] ?? '';
        $this->fromEmail = $_ENV['FROM_EMAIL'] ?? 'noreply@casadeproductie.ro';
        $this->fromName = $_ENV['FROM_NAME'] ?? 'Casa de Producție Filme';
        
        // Model pentru salvarea în DB
        $this->emailMessageModel = new \EmailMessage();
    }

    public function sendEmail($to, $subject, $body, $isHtml = true) {
        // Dacă nu avem credențiale SMTP, doar simulez trimiterea
        if (empty($this->smtpUsername) || empty($this->smtpPassword)) {
            error_log("SIMULARE: Email trimis către: $to, Subject: $subject");
            return true; // Returnează success pentru development
        }
        try {
            $mail = new PHPMailer(true);

            // Server settings
            $mail->isSMTP();
            $mail->Host       = $this->smtpHost;
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->smtpUsername;
            $mail->Password   = $this->smtpPassword;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $this->smtpPort;

            // Recipients
            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($to);

            // Content
            $mail->isHTML($isHtml);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $success = $mail->send();
            error_log("Email trimis către: $to, Subject: $subject, Success: " . ($success ? 'DA' : 'NU'));
            
            return $success;
        } catch (Exception $e) {
            error_log("Eroare PHPMailer: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ACTUALIZAT: Trimite cerere schimbare rol + salvează în DB
     */
    public function sendRoleChangeRequest($userEmail, $userName, $currentRole, $requestedRole, $reason) {
        $adminEmail = $this->getAdminEmail();
        
        $subject = "Cerere schimbare rol - $userName";
        
        $body = $this->buildRoleChangeTemplate($userName, $userEmail, $currentRole, $requestedRole, $reason);
        
        // Trimite email-ul extern
        $emailSent = $this->sendEmail($adminEmail, $subject, $body);
        
        // Salvează în baza de date
        if ($emailSent) {
            // Găsește ID-urile utilizatorilor
            $userModel = new \App\Models\User();
            $user = $userModel->findByEmail($userEmail);
            $admin = $userModel->findByEmail($adminEmail);
            
            if ($user && $admin) {
                $this->emailMessageModel->saveMessage(
                    $user['id'],        // from_user_id
                    $admin['id'],       // to_user_id
                    $subject,           // subject
                    $reason,            // message (motivul cererii)
                    'role_change',      // type
                    null               // project_id
                );
                
                error_log("Cerere schimbare rol salvată în DB pentru user {$user['id']}");
            }
        }
        
        return $emailSent;
    }

    /**
     * ACTUALIZAT: Trimite mesaj despre proiect + salvează în DB
     */
    public function sendProjectMessage($fromUser, $toUser, $projectTitle, $message) {
        $subject = "Mesaj despre proiectul: $projectTitle";
        
        $body = $this->buildProjectMessageTemplate($fromUser, $toUser, $projectTitle, $message);
        
        // Trimite email-ul extern
        $emailSent = $this->sendEmail($toUser['email'], $subject, $body);
        
        // Salvează în baza de date
        if ($emailSent) {
            // Găsește project_id
            $projectModel = new \App\Models\Project();
            $project = $projectModel->findByTitle($projectTitle); // Trebuie să implementezi această metodă
            
            $this->emailMessageModel->saveMessage(
                $fromUser['id'],           // from_user_id
                $toUser['id'],             // to_user_id
                $subject,                  // subject
                $message,                  // message
                'project',                 // type
                $project['id'] ?? null     // project_id
            );
            
            error_log("Mesaj proiect salvat în DB: {$fromUser['id']} → {$toUser['id']}, proiect: $projectTitle");
        }
        
        return $emailSent;
    }

    /**
     * ACTUALIZAT: Trimite mesaj de la staff + salvează în DB
     */
    public function sendStaffToLeaderMessage($staffUser, $leaderUser, $subject, $message) {
        $emailSubject = "Mesaj de la staff: $subject";
        
        $body = $this->buildStaffMessageTemplate($staffUser, $subject, $message);
        
        // Trimite email-ul extern
        $emailSent = $this->sendEmail($leaderUser['email'], $emailSubject, $body);
        
        // Salvează în baza de date
        if ($emailSent) {
            $this->emailMessageModel->saveMessage(
                $staffUser['id'],      // from_user_id
                $leaderUser['id'],     // to_user_id
                $subject,              // subject
                $message,              // message
                'staff_message',       // type
                null                   // project_id (poate fi adăugat dacă e necesar)
            );
            
            error_log("Mesaj staff salvat în DB: {$staffUser['id']} → {$leaderUser['id']}");
        }
        
        return $emailSent;
    }

    /**
     * NOU: Trimite mesaj general între utilizatori
     */
    public function sendGeneralMessage($fromUser, $toUser, $subject, $message, $projectId = null) {
        $body = $this->buildGeneralMessageTemplate($fromUser, $toUser, $subject, $message);
        
        // Trimite email-ul extern
        $emailSent = $this->sendEmail($toUser['email'], $subject, $body);
        
        // Salvează în baza de date
        if ($emailSent) {
            $this->emailMessageModel->saveMessage(
                $fromUser['id'],
                $toUser['id'],
                $subject,
                $message,
                'general',
                $projectId
            );
        }
        
        return $emailSent;
    }

    // TEMPLATE BUILDERS

    private function buildRoleChangeTemplate($userName, $userEmail, $currentRole, $requestedRole, $reason) {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px;'>
            <h2 style='color: #007bff;'>🔄 Cerere de schimbare rol</h2>
            
            <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                <p><strong>Utilizator:</strong> $userName ($userEmail)</p>
                <p><strong>Rol actual:</strong> " . $this->translateRole($currentRole) . "</p>
                <p><strong>Rol solicitat:</strong> " . $this->translateRole($requestedRole) . "</p>
            </div>
            
            <div style='background: #e7f3ff; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                <h3>Motivul cererii:</h3>
                <p>" . nl2br(htmlspecialchars($reason)) . "</p>
            </div>
            
            <hr style='margin: 30px 0;'>
            <p style='color: #666; font-size: 0.9em;'>
                <em>Email generat automat de sistemul Casa de Producție Filme</em><br>
                <em>Data: " . date('d.m.Y H:i:s') . "</em>
            </p>
        </div>";
    }

    private function buildProjectMessageTemplate($fromUser, $toUser, $projectTitle, $message) {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px;'>
            <h2 style='color: #28a745;'>📁 Mesaj nou despre proiect</h2>
            
            <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                <p><strong>De la:</strong> {$fromUser['name']} ({$fromUser['email']})</p>
                <p><strong>Către:</strong> {$toUser['name']}</p>
                <p><strong>Proiect:</strong> $projectTitle</p>
                <p><strong>Data:</strong> " . date('d.m.Y H:i:s') . "</p>
            </div>
            
            <div style='background: #e8f5e8; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                <h3>Mesaj:</h3>
                <p>" . nl2br(htmlspecialchars($message)) . "</p>
            </div>
            
            <hr style='margin: 30px 0;'>
            <p style='color: #666; font-size: 0.9em;'>
                <em>Pentru răspuns, folosește adresa de email: {$fromUser['email']}</em><br>
                <em>Email generat automat de sistemul Casa de Producție Filme</em>
            </p>
        </div>";
    }

    private function buildStaffMessageTemplate($staffUser, $subject, $message) {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px;'>
            <h2 style='color: #dc3545;'>👥 Mesaj nou de la staff</h2>
            
            <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                <p><strong>De la:</strong> {$staffUser['name']} ({$staffUser['email']})</p>
                <p><strong>Rol:</strong> Staff producție</p>
                <p><strong>Data:</strong> " . date('d.m.Y H:i:s') . "</p>
            </div>
            
            <div style='background: #ffe6e6; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                <h3 style='color: #dc3545;'>$subject</h3>
                <p>" . nl2br(htmlspecialchars($message)) . "</p>
            </div>
            
            <hr style='margin: 30px 0;'>
            <p style='color: #666; font-size: 0.9em;'>
                <em>Pentru răspuns urgent, folosește adresa: {$staffUser['email']}</em><br>
                <em>Email generat automat de sistemul Casa de Producție Filme</em>
            </p>
        </div>";
    }

    private function buildGeneralMessageTemplate($fromUser, $toUser, $subject, $message) {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px;'>
            <h2 style='color: #6c757d;'>💬 Mesaj nou</h2>
            
            <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                <p><strong>De la:</strong> {$fromUser['prenume']} ({$fromUser['email']})</p>
                <p><strong>Către:</strong> {$toUser['prenume']}</p>
                <p><strong>Data:</strong> " . date('d.m.Y H:i:s') . "</p>
            </div>
            
            <div style='background: #f0f0f0; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                <h3>$subject</h3>
                <p>" . nl2br(htmlspecialchars($message)) . "</p>
            </div>
            
            <hr style='margin: 30px 0;'>
            <p style='color: #666; font-size: 0.9em;'>
                <em>Pentru răspuns, folosește adresa: {$fromUser['email']}</em><br>
                <em>Email generat automat de sistemul Casa de Producție Filme</em>
            </p>
        </div>";
    }

    private function translateRole($role) {
        $translations = [
            'admin' => 'Administrator',
            'lider_productie' => 'Lider de producție',
            'staff_productie' => 'Staff producție',
            'viewer' => 'Viewer'
        ];
        
        return $translations[$role] ?? ucfirst($role);
    }

    private function getAdminEmail() {
        return 'raluca.ionete@gmail.com';
    }
}