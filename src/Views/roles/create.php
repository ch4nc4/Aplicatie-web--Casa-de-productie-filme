<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adaugă Rol - Casa de Producție Filme</title>
    <style>
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        textarea { height: 100px; resize: vertical; }
        .btn { padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background-color: #0056b3; }
        .btn-success { background-color: #28a745; }
        .btn-success:hover { background-color: #218838; }
        .btn-secondary { background-color: #6c757d; }
        .btn-secondary:hover { background-color: #545b62; }
        .error { color: red; margin-top: 10px; padding: 10px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; }
        .warning { color: #856404; background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .back-link { display: inline-block; margin-bottom: 20px; color: #007bff; text-decoration: none; }
        .empty-data { 
            background-color: #f8f9fa; 
            border: 2px dashed #dee2e6; 
            padding: 20px; 
            border-radius: 8px; 
            margin: 10px 0; 
            text-align: center;
        }
        .add-option { 
            display: flex; 
            align-items: center; 
            gap: 10px; 
            margin-top: 10px;
        }
        .quick-add {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 4px;
            margin-top: 10px;
        }
        .quick-add h4 {
            margin: 0 0 10px 0;
            color: #495057;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/admin/users" class="back-link">← Înapoi la pagina de admin</a>
        
        <h1>Adaugă Rol Nou</h1>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="/roles/store">
            <?php echo csrf_token_field(); ?>
            <div class="form-group">
                <label for="nume">Nume Rol *</label>
                <input type="text" id="nume" name="nume" required 
                       value="<?= isset($_POST['nume']) ? htmlspecialchars($_POST['nume']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="descriere">Descriere Rol</label>
                <textarea id="descriere" name="descriere" placeholder="Descrierea rolului..."><?= isset($_POST['descriere']) ? htmlspecialchars($_POST['descriere']) : '' ?></textarea>
            </div>
            <button type="submit" class="btn btn-success">Salvează Rolul</button>
    </div>

</body>
</html>