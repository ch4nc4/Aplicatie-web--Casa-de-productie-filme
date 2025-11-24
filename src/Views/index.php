<?php
session_start();
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$userName = $isLoggedIn ? $_SESSION['user_name'] ?? 'Utilizator' : null;
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casa de Producție Filme Independentă</title>
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
            text-align: center;
            background: rgba(255, 255, 255, 0.37);
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            max-width: 500px;
        }
        .logo {
            font-size: 2.5em;
            font-weight: bold;
            color: black;
            margin-bottom: 10px;
        }
        .subtitle {
            color: black;
            margin-bottom: 30px;
            font-size: 1.1em;
        }
        .welcome {
            color: black;
            margin-bottom: 20px;
            font-weight: bold;
            font-size: 1.1em;
        }
        .btn {
            display: block;
            width: 80%;
            margin: 15px auto;
            padding: 15px 20px;
            background-color:rgb(157, 164, 171);
            color: black;
            text-decoration: none;
            border-radius: 8px;
            font-size: 1.1em;
            font-weight: normal;
            transition: all 0.3s ease;
            border: 1px solid;
            cursor: pointer;
        }
        .btn:hover {
            background-color: rgba(102, 15, 34, 0.75);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(54, 57, 61, 0.3);
            color: white;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #545b62;
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
        }
        .btn-logout {
            background-color: #dc3545;
            color: white;
        }
        .btn-logout:hover {
            background-color: #c82333;
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }
        .btn-signup {
            background-color:rgba(21, 21, 21, 0.82);
            color: white;
        }
        .btn-signup:hover {
            background-color: #218838;
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        .navigation {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .auth-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 15px;
        }
        .auth-buttons .btn {
            width: auto;
            margin: 0;
            padding: 10px 20px;
            font-size: 1em;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">Casa de Producție</div>
        <div class="subtitle">Filme Independente</div>
        
        <?php if ($isLoggedIn): ?>
            <div class="welcome">
                Bine ai venit, <?= htmlspecialchars($userName) ?>!
            </div>
        <?php endif; ?>
        
        <div class="navigation">
            <?php if (!$isLoggedIn): ?>
                <div class="auth-buttons">
                    <a href="/login" class="btn">
                        Login
                    </a>
                    <a href="/signup" class="btn btn-signup">
                        Înregistrare
                    </a>
                </div>
            <?php endif; ?>

            <a href="/views/projects" class="btn">
                Vezi Proiecte
            </a>

            <a href="/views/statuses" class="btn">
                Vezi Statusuri Proiecte
            </a>

            <a href="/views/descriptions" class="btn btn-secondary">
                Despre Aplicație
            </a>

            <?php if ($isLoggedIn): ?>
                <a href="/logout" class="btn btn-logout">
                    Logout
                </a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>