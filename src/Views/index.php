<?php
session_start();
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$userName = $isLoggedIn ? $_SESSION['user_name'] ?? 'Utilizator' : null;

$unreadCount = 0;
if ($isLoggedIn) {
    try {
        require_once __DIR__ . '/../Models/EmailMessage.php';
        $emailModel = new EmailMessage();
        $unreadCount = $emailModel->getUnreadCount($_SESSION['user_id']);
    } catch (Exception $e) {
        $unreadCount = 0;
    }
}
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
        
        /* Email widget în colțul dreapta sus */
        .email-widget {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
            border: 2px solid rgba(102, 15, 34, 0.2);
        }
        
        .email-header {
            text-align: center;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid rgba(102, 15, 34, 0.2);
        }
        
        .email-title {
            font-weight: bold;
            color: rgb(102, 15, 34);
            font-size: 1.1em;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .unread-badge {
            background: #dc3545;
            color: white;
            font-size: 0.7em;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 10px;
            min-width: 16px;
            text-align: center;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .email-buttons {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .email-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            text-decoration: none;
            color: #333;
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(102, 15, 34, 0.2);
            border-radius: 6px;
            transition: all 0.2s ease;
            font-size: 0.9em;
            min-width: 160px;
        }
        
        .email-btn:hover {
            background: rgba(102, 15, 34, 0.1);
            color: rgb(102, 15, 34);
            border-color: rgb(102, 15, 34);
            transform: translateY(-1px);
        }
        
        .email-btn .icon {
            font-size: 1.1em;
        }
        
        .email-btn .text {
            flex: 1;
            text-align: left;
        }
        
        /* Responsive pentru mobile */
        @media (max-width: 768px) {
            .email-widget {
                top: 10px;
                right: 10px;
                padding: 12px;
                max-width: 200px;
            }
            
            .email-btn {
                padding: 6px 10px;
                font-size: 0.8em;
                min-width: 140px;
            }
            
            .email-title {
                font-size: 1em;
            }
        }
        
        /* Stilurile existente pentru restul paginii */
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
            margin: 8px auto;
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
            gap: 5px;
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
        <?php if ($isLoggedIn && !in_array('Admin', $_SESSION['roles'] ?? [])): ?>
        <div class="email-widget">
            <div class="email-header">
                <h4 class="email-title">
                    📧 Mesaje
                    <?php if ($unreadCount > 0): ?>
                        <span class="unread-badge"><?= $unreadCount ?></span>
                    <?php endif; ?>
                </h4>
            </div>
                
            <a href="/messages/compose" class="email-btn">
                <span class="icon">✉️</span>
                <span class="text">Trimite nou</span>
            </a>
        </div>
    <?php endif; ?>

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

            <a href="/crew" class="btn">
                Vezi Crew
            </a>

            <?php if ($isLoggedIn): ?>
                <a href="/user/profile" class="btn">
                    Vezi Profil
                </a>
            <?php endif; ?>

            <?php if ($isLoggedIn && in_array('Admin', $_SESSION['roles'] ?? [])): ?>
                <a href="/admin/users" class="btn">
                    Vezi Useri
                </a>
                <a href="/statistics" class="btn">
                    📊 Statistici Dashboard
                </a>
            <?php endif; ?>

            <a href="/views/projects" class="btn">
                Vezi Proiecte
            </a>

            <?php if ($isLoggedIn && !in_array('Utilizator autentificat', $_SESSION['roles'] ?? [])): ?>
                <a href="/views/statuses" class="btn">
                    Vezi Statusuri Proiecte
                </a>
            <?php endif; ?>

            <?php if ($isLoggedIn): ?>
                <a href="/views/watchlist" class="btn">
                    Vezi Watchlist
                </a>
            <?php endif; ?>

            <a href="/top-movies" class="btn">
                🎬 Top Filme Box Office
            </a>

            <?php if (!in_array('Admin', $_SESSION['roles'] ?? [])): ?>
               <a href="/views/descriptions" class="btn btn-secondary">
                Despre Aplicație
                 </a>
            <?php endif; ?>

            <?php if ($isLoggedIn): ?>
                <a href="/logout" class="btn btn-logout">
                    Logout
                </a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>