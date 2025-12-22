<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Înregistrare - Casa de Producție</title>
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
        .form-row {
            display: flex;
            gap: 10px;
        }
        .form-row .form-group {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/" class="back-link">← Înapoi la pagina principală</a>
        
        <div class="logo">📝 Înregistrare</div>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (isset($showLoginOption) && $showLoginOption): ?>
            <div class="info">
                Ai deja cont? <a href="/login">Intră în cont aici</a>
            </div>
        <?php endif; ?>

        <form method="POST" action="/auth/signup">
            <?php echo csrf_token_field(); ?>
            <div class="form-row">
                <div class="form-group">
                    <label for="prenume">Prenume:</label>
                    <input type="text" id="prenume" name="prenume" required 
                           value="<?= isset($_POST['prenume']) ? htmlspecialchars($_POST['prenume']) : '' ?>">
                </div>
                <div class="form-group">
                    <label for="nume_familie">Nume:</label>
                    <input type="text" id="nume_familie" name="nume_familie" required 
                           value="<?= isset($_POST['nume_familie']) ? htmlspecialchars($_POST['nume_familie']) : '' ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required 
                       value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Parolă (min. 6 caractere):</label>
                <input type="password" id="password" name="password" required minlength="6">
            </div>
            
            <button type="submit" class="btn">Creează cont</button>
        </form>

        <div class="links">
            <a href="/login">Ai deja cont? Intră în cont</a>
        </div>
    </div>
</body>
</html>