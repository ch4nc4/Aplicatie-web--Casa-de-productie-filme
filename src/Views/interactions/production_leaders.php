<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesaj către lider de producție - Casa de Producție Filme</title>
    <style>
        /* Same styles as above */
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container { 
            max-width: 600px; 
            margin: 0 auto; 
            padding: 20px; 
            background: white;
            min-height: 100vh;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px 0;
            border-bottom: 2px solid #007bff;
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
        select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        textarea {
            height: 120px;
            resize: vertical;
        }
        .btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-secondary:hover {
            background: #545b62;
        }
        .alert {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📧 Mesaj despre proiect</h1>
            <p>Trimite un mesaj altui lider de producție despre un proiect</p>
        </div>

        <div id="alerts"></div>

        <form id="projectMessageForm">
            <div class="form-group">
                <label for="to_user_id">Către liderul:</label>
                <select id="to_user_id" name="to_user_id" required>
                    <option value="">Selectează liderul de producție</option>
                    <?php foreach ($leaders as $leader): ?>
                        <option value="<?= $leader['id'] ?>">
                            <?= htmlspecialchars($leader['name']) ?> (<?= htmlspecialchars($leader['email']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="project_id">Proiectul:</label>
                <select id="project_id" name="project_id" required>
                    <option value="">Selectează proiectul</option>
                    <?php foreach ($projects as $project): ?>
                        <option value="<?= $project['id'] ?>">
                            <?= htmlspecialchars($project['title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="message">Mesajul:</label>
                <textarea id="message" name="message" required 
                         placeholder="Scrie mesajul despre proiect..."></textarea>
            </div>

            <button type="submit" class="btn">📧 Trimite mesajul</button>
            <a href="/dashboard" class="btn btn-secondary">← Înapoi</a>
        </form>
    </div>

    <script>
    document.getElementById('projectMessageForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        try {
            const response = await fetch('/email/project-message', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            const alertDiv = document.getElementById('alerts');
            if (result.success) {
                alertDiv.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
                this.reset();
            } else {
                alertDiv.innerHTML = `<div class="alert alert-error">${result.message}</div>`;
            }
        } catch (error) {
            document.getElementById('alerts').innerHTML = 
                '<div class="alert alert-error">Eroare la trimiterea mesajului</div>';
        }
    });
    </script>
</body>
</html>