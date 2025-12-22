<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Watchlist - Casa de Producție Filme</title>
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
        .header {
            margin-bottom: 30px;
        }
        .title {
            font-size: 2em;
            font-weight: normal;
            color: black;
            margin-bottom: 10px;
        }
        .subtitle {
            color: black;
            margin-bottom: 30px;
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
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(54, 57, 61, 0.3);
        }
        .btn-success {
            background-color: #28a745;
        }
        .btn-success:hover {
            background-color: #218838;
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .btn-warning:hover {
            background-color: #e0a800;
            box-shadow: 0 5px 15px rgba(255, 193, 7, 0.3);
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color:rgb(8, 12, 16);
            text-decoration: none;
            font-weight: bold;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/" class="back-link">← Înapoi la pagina principală</a>
        
        <div class="header">
            <div class="title">Gestionare watchlist</div>
            <div class="subtitle">Operațiuni disponibile</div>
        </div>
        
        <div class="navigation">
            <a href="/watchlist/show" class="btn">
                Vizualizează toate obiectele din watchlist
            </a>
            
            <a href="/watchlist/create" class="btn">
                Creează element nou în watchlist
            </a>
        </div>
    </div>
</body>
</html>