<?php
require_once __DIR__ . '/../../../config/env.php';

$recaptchaSiteKey = env('RECAPTCHA_SITE_KEY');

?>


<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Casa de Producție</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg,rgb(102, 15, 34) 0%,rgb(36, 36, 101) 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            width: 100%;
        }
        .logo {
            text-align: center;
            font-size: 2em;
            font-weight: bold;
            margin-bottom: 30px;
            color: #333;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .btn {
            width: 100%;
            padding: 12px;
            background-color: rgb(102, 15, 34);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            margin-bottom: 15px;
        }
        .btn:hover {
            background-color: rgb(82, 12, 27);
        }
        .btn-secondary {
            background-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #545b62;
        }
        .error {
            color: #dc3545;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
        }
        .info {
            color: #0c5460;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 4px;
        }
        .links {
            text-align: center;
            margin-top: 20px;
        }
        .links a {
            color: rgb(102, 15, 34);
            text-decoration: none;
            margin: 0 10px;
        }
        .links a:hover {
            text-decoration: underline;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: rgb(102, 15, 34);
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/" class="back-link">← Înapoi la pagina principală</a>
        
        <div class="logo">Login</div>

        <details style="margin-bottom: 20px;">
            <summary style="cursor:pointer;font-weight:bold;color:rgb(102, 15, 34);font-size:1.05em;">
                Despre roluri
            </summary>
            <div style="margin-top:10px; color:#333; font-size:0.98em;">
                În momentul în care un user se autentifică, acesta are rol de <strong>utilizator autentificat</strong>.<br>
                Dacă acesta alege să navigheze aplicația fără să se autentifice, rolul lui va fi de <strong>utilizator extern</strong>.<br>
                Pentru roluri cu mai multe permisiuni consultați acest fișier care descrie rolurile valabile:
                <a href="/../../../public/roluri.txt" target="_blank" style="color:#007bff;text-decoration:underline;">→ Descriere roluri</a><br>
                În eventualitatea în care decideți că vă doriți un alt rol, puteți contacta admin-ul acestui website la adresa de e-mail: <a href="mailto:raluca.ionete@gmail.com">raluca.ionete@gmail.com</a>
            </div>
        </details>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (isset($showSignupOption) && $showSignupOption): ?>
            <div class="info">
                Nu ai cont? <a href="/signup">Creează-ți unul aici</a>
            </div>
        <?php endif; ?>

      <?php if (isset($needsPasswordSetup) && $needsPasswordSetup): ?>
            <form method="POST" action="/auth/set-password">
                <?php echo csrf_token_field(); ?>
                <input type="hidden" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                <div class="info">
                    Contul tău nu are parolă setată. Te rugăm să setezi o parolă nouă mai jos:
                </div>
                <div class="form-group">
                    <label for="new_password">Parolă nouă:</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirmă parola:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn">Setează parola</button>
            </form>
        <?php else: ?>
            <form method="POST" action="/auth/login">
                <?php echo csrf_token_field(); ?>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required 
                        value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Parolă:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="g-recaptcha" data-sitekey="<?= htmlspecialchars($recaptchaSiteKey) ?>"></div><br>
                
                <button type="submit" class="btn">Intră în cont</button>
            </form>
        <?php endif; ?>

        <div class="links">
            <a href="/signup">Nu ai cont? Înregistrează-te</a>
        </div>
    </div>
</body>
</html>