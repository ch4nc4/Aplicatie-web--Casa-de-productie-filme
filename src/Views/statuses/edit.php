<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editează Status - Casa de Producție</title>
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
        .btn-success {
            background-color: #28a745;
        }
        .btn-success:hover {
            background-color: #218838;
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
        .info {
            color: #0c5460;
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
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
        h1, h2 {
            color: #333;
            margin-bottom: 30px;
        }
        .readonly-field { 
            background-color: #f8f9fa; 
            color: #6c757d; 
        }
        .datetime-input {
            position: relative;
        }
        .datetime-input input[type="datetime-local"] {
            width: 100%;
        }
        .status-info {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .status-info h3 {
            margin: 0 0 10px 0;
            color: #495057;
        }
        .warning {
            color: #856404;
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .id-selector {
            background-color: #f8f9fa;
            border: 2px solid #007bff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/views/statuses" class="back-link">← Înapoi la statusuri</a>
        
        <h1>Editează Status</h1>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if (!isset($status) || empty($status)): ?>
            <div class="id-selector">
                <h2>Selectează Statusul de Editat</h2>
                <form method="GET" action="/statuses/edit">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="id">ID Status <span class="required">*</span></label>
                            <input type="number" id="id" name="id" required min="1"
                                   value="<?= isset($_GET['id']) ? htmlspecialchars($_GET['id']) : '' ?>"
                                   placeholder="Introdu ID-ul statusului">
                            <div class="help-text">Introdu ID-ul statusului pe care vrei să îl editezi</div>
                        </div>
                        <div class="form-group" style="display: flex; align-items: end;">
                            <button type="submit" class="btn btn-success">Caută Status</button>
                        </div>
                    </div>
                </form>

                <?php 
                try {
                    require_once __DIR__ . '/../../Models/StatusProject.php';
                    $statusModel = new StatusProject();
                    $allStatuses = $statusModel->getAll();
                    if (!empty($allStatuses)): 
                ?>
                    <div style="margin-top: 20px;">
                        <h3>Statusuri Disponibile:</h3>
                        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                            <?php foreach ($allStatuses as $stat): ?>
                                <a href="/statuses/edit?id=<?= $stat['id'] ?>" 
                                   class="btn btn-secondary" 
                                   style="padding: 8px 12px; font-size: 14px;">
                                    ID: <?= $stat['id'] ?> - <?= htmlspecialchars(substr($stat['nume'], 0, 20)) ?><?= strlen($stat['nume']) > 20 ? '...' : '' ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php 
                    endif;
                } catch (Exception $e) {
                }
                ?>
            </div>

        <?php else: ?>            
            <?php if (isset($status['projects_count']) && $status['projects_count'] > 0): ?>
                <div class="warning">
                    <strong>Atenție!</strong> Acest status este folosit de <?= $status['projects_count'] ?> proiect(e). 
                    Modificările vor afecta toate proiectele asociate.
                </div>
            <?php endif; ?>

            <div class="status-info">
                <h3>Informații Status</h3>
                <p><strong>ID:</strong> <?= htmlspecialchars($status['id']) ?></p>
                <p><strong>Nume Curent:</strong> <?= htmlspecialchars($status['nume']) ?></p>
                <p><strong>Status:</strong> 
                    <?php if (!empty($status['data_finalizare'])): ?>
                        <span style="color: #28a745;">Completat (<?= date('d.m.Y H:i', strtotime($status['data_finalizare'])) ?>)</span>
                    <?php elseif (!empty($status['data_start']) && strtotime($status['data_start']) <= time()): ?>
                        <span style="color: #ffc107;">În progres (de la <?= date('d.m.Y H:i', strtotime($status['data_start'])) ?>)</span>
                    <?php else: ?>
                        <span style="color: #6c757d;">Planificat</span>
                    <?php endif; ?>
                </p>
                
                <p>
                    <a href="/statuses/edit" class="btn btn-secondary" style="padding: 8px 12px; font-size: 14px;">
                        Editează alt status
                    </a>
                </p>
            </div>
            
            <form method="POST" action="/statuses/update">
                <input type="hidden" name="id" value="<?= $status['id'] ?>">
                
                <div class="form-group">
                    <label for="nume">Nume Status <span class="required">*</span></label>
                    <input type="text" id="nume" name="nume" required maxlength="100"
                           value="<?= htmlspecialchars($status['nume'] ?? '') ?>"
                           placeholder="Ex: În dezvoltare, Pre-producție, Filmări, etc.">
                    <div class="help-text">Numele statusului (maxim 100 caractere)</div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="data_start">Data Start <span class="required">*</span></label>
                        <div class="datetime-input">
                            <input type="datetime-local" id="data_start" name="data_start" required
                                   value="<?= isset($status['data_start']) ? date('Y-m-d\TH:i', strtotime($status['data_start'])) : '' ?>">
                        </div>
                        <div class="help-text">Când începe acest status</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="data_finalizare">Data Finalizare</label>
                        <div class="datetime-input">
                            <input type="datetime-local" id="data_finalizare" name="data_finalizare"
                                   value="<?= isset($status['data_finalizare']) && $status['data_finalizare'] ? date('Y-m-d\TH:i', strtotime($status['data_finalizare'])) : '' ?>">
                        </div>
                        <div class="help-text">Opțional - când se finalizează statusul</div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="nota_aditionala">Notă Adițională</label>
                    <textarea id="nota_aditionala" name="nota_aditionala" 
                              placeholder="Informații adiționale despre acest status, instrucțiuni speciale, observații..."><?= htmlspecialchars($status['nota_aditionala'] ?? '') ?></textarea>
                    <div class="help-text">Detalii suplimentare despre status (opțional)</div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn">Salvează Modificările</button>
                    <a href="/views/statuses" class="btn btn-secondary" style="margin-left: 10px;">Anulează</a>
                    <a href="/statuses/edit" class="btn btn-secondary" style="margin-left: 10px;">Editează Alt Status</a>
                    
                    <?php if (isset($status['projects_count']) && $status['projects_count'] == 0): ?>
                        <a href="/statuses/delete/<?= $status['id'] ?>" class="btn" 
                           style="background-color: #dc3545; margin-left: 10px;"
                           onclick="return confirm('Ești sigur că vrei să ștergi acest status?')">
                            Șterge Status
                        </a>
                    <?php endif; ?>
                </div>
            </form>

            <?php if (isset($status['projects_count']) && $status['projects_count'] > 0): ?>
                <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #dee2e6;">
                    <h3>Proiecte care folosesc acest status:</h3>
                    <p>Acest status este folosit de <strong><?= $status['projects_count'] ?></strong> proiect(e).</p>
                    <p><a href="/projects/show" class="btn btn-secondary">Vezi proiecte →</a></p>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>

    <script>
        function validateDates() {
            const startDate = document.getElementById('data_start');
            const endDate = document.getElementById('data_finalizare');
             // If elements don't exist, skip validation
            
            if (startDate.value && endDate.value && new Date(endDate.value) <= new Date(startDate.value)) {
                alert('Data de finalizare trebuie să fie după data de start!');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>