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
    <title>Cerere schimbare rol - Casa de Producție Filme</title>
    <style>
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
            height: 100px;
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
            <h1>📧 Cerere schimbare rol</h1>
            <p>Trimite o cerere administratorului pentru schimbarea rolului tău</p>
        </div>

        <div id="alerts"></div>

        <form id="roleChangeForm">
            <div class="form-group">
                <label for="requested_role">Rol solicitat:</label>
                <select id="requested_role" name="requested_role" required>
                    <option value="">Selectează rolul dorit</option>
                    <option value="lider_productie">Lider de producție</option>
                    <option value="staff_productie">Staff producție</option>
                    <option value="viewer">Viewer</option>
                </select>
            </div>

            <div class="form-group">
                <label for="reason">Motivul cererii:</label>
                <textarea id="reason" name="reason" required 
                         placeholder="Explică de ce soliciti această schimbare de rol..."></textarea>
            </div>

            <button type="submit" class="btn">📧 Trimite cererea</button>
            <a href="/dashboard" class="btn btn-secondary">← Înapoi</a>
        </form>
    </div>

    <script>
    document.getElementById('roleChangeForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        try {
            const response = await fetch('/email/role-change', {
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
                '<div class="alert alert-error">Eroare la trimiterea cererii</div>';
        }
    });
    </script>
</body>
</html>