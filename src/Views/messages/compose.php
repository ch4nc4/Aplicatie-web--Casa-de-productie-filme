<?php
session_start();

error_log("SMTP_USERNAME din ENV: " . ($_ENV['SMTP_USERNAME'] ?? 'NU EXISTĂ'));
error_log("SMTP_PASSWORD din ENV: " . ($_ENV['SMTP_PASSWORD'] ?? 'NU EXISTĂ'));


$returnMsg = ''; 

#email Gmail
require_once __DIR__ . '/../../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
 
if(isset($_POST['submit'])){ 
    
    // Form fields validation check
    if(!empty($_POST['to_admin']) && !empty($_POST['subject']) && !empty($_POST['message'])){ 
         
        // reCAPTCHA checkbox validation (opțional - poți comenta dacă nu vrei)
        /*
        if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])){ 
            // Google reCAPTCHA API secret key 
            $secret_key = 'SECRET KEY'; 
             
            // reCAPTCHA response verification
            $verify_captcha = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret_key.'&response='.$_POST['g-recaptcha-response']); 
            
            // Decode reCAPTCHA response 
            $verify_response = json_decode($verify_captcha); 
             
            // Check if reCAPTCHA response returns success 
            if($verify_response->success){ 
        */
                
                // Verifică autentificarea
                if(!isset($_SESSION['user_id'])) {
                    $returnMsg = 'Trebuie să fii autentificat pentru a trimite cereri.';
                } else {
                    // Include modelele necesare
                    require_once(__DIR__ . '/../../Models/User.php');
                    require_once(__DIR__ . '/../../Models/EmailMessage.php');
                    
                    $to_admin = $_POST['to_admin']; 
                    $subject = $_POST['subject'];
                    $message = $_POST['message'];
                    
                    // Obține datele utilizatorului curent și admin
                    $userModel = new User();
                    $currentUser = $userModel->findById($_SESSION['user_id']);
                    $adminUser = $userModel->findById($to_admin);
                 
                        
                $name = $currentUser['prenume'] . ' ' . $currentUser['nume_familie'];
                $email = $currentUser['email'];

                $mailBody = "=== CERERE CĂTRE ADMINISTRATOR ===\n\n";
                $mailBody .= "Utilizator: " . $name . "\n";
                $mailBody .= "Email: " . $email . "\n";
                $mailBody .= "Subiect: " . $subject . "\n";
                $mailBody .= "Data: " . date('d.m.Y H:i:s') . "\n\n";
                $mailBody .= "Mesaj:\n" . $message . "\n\n";
                $mailBody .= "--- Pentru răspuns, folosește adresa: " . $email . " ---\n";
                $mailBody .= "Email generat automat de sistemul Casa de Producție Filme";
                
                $mail = new PHPMailer(true); 

                $mail->isSMTP();

                try {
                    // Configurări SMTP din .env (sau setează direct)
                    $mail->SMTPDebug = 0; // 0 = nu afișa debug în producție                     
                    $mail->SMTPAuth = true; 

                    $toEmail = 'raluca.ionete@gmail.com'; // EMAIL-UL TĂU (development mode)
                    $nume = 'Admin Casa de Producție';

                    $mail->SMTPSecure = "tls"; // sau "ssl"                
                    $mail->Host = "smtp.gmail.com";      
                    $mail->Port = 587; // sau 465 pentru SSL                 
                    $mail->Username = $_ENV['SMTP_USERNAME'];  // Gmail username
                    $mail->Password = $_ENV['SMTP_PASSWORD'];     // Gmail app password
                    
                    $mail->addReplyTo($email, $name); // Reply către utilizatorul real
                    $mail->addAddress($toEmail, $nume);
                    
                    $mail->setFrom($mail->Username, 'Casa de Productie Filme - Sistem');
                    $mail->Subject = '[CERERE ADMIN] ' . $subject;
                    $mail->Body = nl2br($mailBody);
                    $mail->isHTML(true);
                    
                    $emailSent = $mail->send();
                    
                    if($emailSent) {
                        error_log("Email trimis: " . ($emailSent ? 'SUCCESS' : 'FAILED'));

                        // Salvează în baza de date
                        $emailMessageModel = new EmailMessage();
                        $saved = $emailMessageModel->saveMessage(
                            $currentUser['id'],    // from_user_id
                            $adminUser['id'],      // to_user_id  
                            $subject,              // subject
                            $message,              // message
                            'general',       // type
                            null                   // project_id
                        );
                        
                        if($saved) {
                            $returnMsg = 'Cererea ta a fost trimisă cu succes către administrator!'; 
                        } else {
                            $returnMsg = 'Email trimis, dar a apărut o problemă la salvare în sistem.';
                        }
                    } else {
                        $returnMsg = 'Eroare la trimiterea email-ului. Încearcă din nou.';
                    }
                    
                }
                catch (Exception $e) {
                    error_log("Eroare PHPMailer: " . $e->getMessage());
                    $returnMsg = 'Eroare la trimiterea email-ului: ' . $e->getMessage();
                }
                    
                header('Location: /');
                exit;
                 
        /*    
            } else{ 
                $returnMsg = 'Please check the CAPTCHA box.'; 
            } 
        */
            }
    } else { 
        $returnMsg = 'Te rog completează toate câmpurile obligatorii.'; 
    } 
} 

?>

<!DOCTYPE html>
<html>
  <head>
    <title>Cerere către administrator - Casa de Producție Filme</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
    .label {margin: 2px 0;}
    .field {margin: 0 0 20px 0;}	
        .content {width: 500px;margin: 0 auto;}
        h1, h2 {font-family:"Georgia", Times, serif;font-weight: normal;}
        div#central {margin: 40px 0px 100px 0px;}
        @media all and (min-width: 368px) and (max-width: 479px) {.content {width: 350px;}}
        @media all and (max-width: 367px) {
            body {margin: 0 auto;word-wrap:break-word}
            .content {width:auto;}
            div#central {	margin: 40px 20px 100px 20px;}
        }
        body {font-family: 'Helvetica',Arial,sans-serif;background:#ffffff;margin: 0 auto;-webkit-font-smoothing: antialiased;  font-size: initial;line-height: 1.7em;}	
        input, textarea, select {width:100%;padding: 15px;font-size:1em;border: 1px solid #A1A1A1;box-sizing: border-box;}
        input[type="submit"] {
            padding: 12px 60px;
            background: #ffc107;
            border: none;
            color: rgb(40, 40, 40);
            font-size:1em;
            font-family: "Georgia", Times, serif;
            cursor: pointer;
            width: auto;
        }
        input[type="submit"]:hover {
            background: #e0a800;
        }
        #message {  padding: 0px 40px 0px 0px; }
        #mail-status {
            padding: 12px 20px;
            width: 100%;
            font-size: 1em;
            font-family: "Georgia", Times, serif;
            color: rgb(40, 40, 40);
            margin-bottom: 20px;
        }
      .error{background-color: #F7902D; margin-bottom: 40px;}
      .success{background-color: #48e0a4; margin-bottom: 40px;}
        .g-recaptcha {margin: 0 0 25px 0;}
        .back-link {margin-bottom: 20px;}
        .back-link a {color: #007bff; text-decoration: none;}
        .back-link a:hover {text-decoration: underline;}
    </style>
    
  </head>
  <body>
<div id="central">
    <div class="content">
        <?php if($returnMsg): ?>
            <div id="mail-status" class="<?= (strpos($returnMsg, 'succes') !== false) ? 'success' : 'error' ?>" style="display: block;">
                <?= htmlspecialchars($returnMsg) ?>
            </div>
        <?php endif; ?>

        <div class="back-link">
            <a href="/">← Înapoi la meniul principal</a>
        </div>
        
        <h1>Cerere către administrator</h1>
        <p>Folosește acest formular pentru cereri importante, probleme tehnice sau schimbări de rol:</p>
        <div id="message">
            

<form action="" method="post">
    <input type="hidden" name="to_admin" value="2">

    <div class="label">Cererea va fi trimisă către administrator</div>
    <div class="field">
        <div style="background: #d1ecf1; padding: 15px; border: 1px solid #bee5eb; border-radius: 4px; color: #0c5460;">
            ℹ️ <strong>Cererea ta va fi trimisă către administratorul sistemului</strong>
        </div>
    </div>
   
   <div class="label">Subiectul cererii:</div>
            <div class="field">			
                <input type="text" id="subject" name="subject" placeholder="Ex: Cerere schimbare rol, Problemă tehnică..." required>
            </div>
    <div class="label">Descrierea detaliată:</div>
            <div class="field">			
                <textarea id="message" name="message" rows="8" placeholder="Descrie cererea ta în detaliu..." required></textarea>			
            </div>
    
    <!-- Decomentează dacă vrei reCAPTCHA -->
    <!-- <div class="g-recaptcha" data-sitekey="SITE KEY"></div> -->
    
    <input type="submit" name="submit" value="TRIMITE CEREREA" >
</form>
        </div>		
    </div><!-- content -->
</div><!-- central -->	
</body>
</html>