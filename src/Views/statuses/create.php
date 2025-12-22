<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adaugă Status Nou - Casa de Producție Filme</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .container { 
            max-width: 800px; 
            margin: 0 auto; 
            padding: 20px; 
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-group { 
            margin-bottom: 20px; 
        }
        .form-row {
            display: flex;
            gap: 15px;
        }
        .form-row .form-group {
            flex: 1;
        }
        label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: bold; 
            color: #333;
        }
        input, select, textarea { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #ddd; 
            border-radius: 4px; 
            font-size: 16px;
            box-sizing: border-box;
        }
        textarea { 
            height: 120px; 
            resize: vertical; 
            font-family: Arial, sans-serif;
        }
        .btn { 
            padding: 12px 24px; 
            background-color: #007bff; 
            color: white; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
        }
        .btn:hover { 
            background-color: #0056b3; 
        }
        .btn-secondary {
            background-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #545b62;
        }
        .error { 
            color: #dc3545; 
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px; 
        }
        .success {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .back-link { 
            display: inline-block; 
            margin-bottom: 20px; 
            color: #007bff; 
            text-decoration: none; 
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .required {
            color: red;
        }
        .help-text {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
        }
        .datetime-input {
            position: relative;
        }
        .datetime-input input[type="datetime-local"] {
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/views/statuses" class="back-link">← Înapoi la statusuri</a>
        
        <h1>Adaugă Status Nou</h1>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="/statuses/store">
            <?php echo csrf_token_field(); ?>
            <div class="form-group">
                <label for="nume">Nume Status <span class="required">*</span></label>
                <input type="text" id="nume" name="nume" required maxlength="100"
                       value="<?= isset($_POST['nume']) ? htmlspecialchars($_POST['nume']) : '' ?>"
                       placeholder="Ex: În dezvoltare, Pre-producție, Filmări, etc.">
                <div class="help-text">Numele statusului (maxim 100 caractere)</div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="data_start">Data Start <span class="required">*</span></label>
                    <div class="datetime-input">
                        <input type="datetime-local" id="data_start" name="data_start" required
                               value="<?= isset($_POST['data_start']) ? htmlspecialchars($_POST['data_start']) : date('Y-m-d\TH:i') ?>">
                    </div>
                    <div class="help-text">Când începe acest status</div>
                </div>
                <div class="form-group">
                    <label for="data_finalizare">Data Finalizare</label>
                    <div class="datetime-input">
                        <input type="datetime-local" id="data_finalizare" name="data_finalizare">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="nota_aditionala">Notă Adițională</label>
                <textarea id="nota_aditionala" name="nota_aditionala"></textarea>
            </div>
            <button type="submit" class="btn btn-success">Salvează Statusul</button>
        </form>
    </div>

    <script>
        // Validate that end date is after start date
        function validateDates() {
            const startDate = document.getElementById('data_start').value;
            const endDate = document.getElementById('data_finalizare').value;
            
            if (startDate && endDate && new Date(endDate) <= new Date(startDate)) {
                alert('Data de finalizare trebuie să fie după data de start!');
                return false;
            }
            return true;
        }

        // Add validation to form submit
        document.querySelector('form').addEventListener('submit', function(e) {
            if (!validateDates()) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>