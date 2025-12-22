<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Șterge Status<?= isset($status) ? ' - ' . htmlspecialchars($status['nume']) : '' ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            p                <form method="POST" action="/statuses/destroy" id="deleteForm">
                    <?php echo csrf_token_field(); ?>
                    <!-- Hidden ID field -->
                    <input type="hidden" name="id" value="<?= $status['id'] ?>"">ing: 0;
            background-color: #f8f9fa;
        }
        .container { 
            max-width: 700px; 
            margin: 50px auto; 
            padding: 30px; 
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
        input, select { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #ddd; 
            border-radius: 4px; 
            font-size: 16px;
            box-sizing: border-box;
        }
        .warning-box { 
            background-color: #f8d7da; 
            border: 1px solid #f5c6cb; 
            padding: 20px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
        }
        .danger-box {
            background-color: #dc3545;
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .warning-title { 
            color: #721c24; 
            font-weight: bold; 
            margin-bottom: 10px;
            font-size: 1.2em;
        }
        .warning-text { 
            color: #721c24; 
        }
        .status-info-box { 
            background: #e9ecef; 
            padding: 20px; 
            border-radius: 8px; 
            margin: 20px 0; 
        }
        .btn { 
            padding: 12px 25px; 
            text-decoration: none; 
            border-radius: 4px; 
            margin: 10px; 
            display: inline-block; 
            font-weight: bold; 
            cursor: pointer; 
            border: none;
            font-size: 16px;
        }
        .btn-danger { 
            background-color: #dc3545; 
            color: white; 
        }
        .btn-danger:hover { 
            background-color: #c82333; 
        }
        .btn-secondary { 
            background-color: #6c757d; 
            color: white; 
        }
        .btn-secondary:hover { 
            background-color: #545b62; 
        }
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .actions { 
            text-align: center; 
            margin-top: 30px; 
        }
        .error { 
            color: #dc3545; 
            margin-bottom: 15px; 
            padding: 10px; 
            background-color: #f8d7da; 
            border: 1px solid #f5c6cb; 
            border-radius: 4px; 
        }
        .success { 
            color: #155724; 
            margin-bottom: 15px; 
            padding: 10px; 
            background-color: #d4edda; 
            border: 1px solid #c3e6cb; 
            border-radius: 4px; 
        }
        .back-link { 
            color: #007bff; 
            text-decoration: none; 
            margin-bottom: 20px; 
            display: inline-block; 
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .status-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 15px 0;
        }
        .detail-item {
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        .detail-label {
            font-weight: bold;
            color: #333;
            display: block;
            margin-bottom: 5px;
        }
        .detail-value {
            color: #666;
        }
        h1, h2 {
            color: #333;
        }
        .projects-warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
        }
        .cannot-delete {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
        }
        .id-selector {
            background-color: #ffe6e6;
            border: 2px solid #dc3545;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .required {
            color: red;
        }
        .help-text {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/views/statuses" class="back-link">← Înapoi la statusuri</a>
        
        <h1>Șterge Status</h1>

        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <!-- Step 1: Choose Status ID -->
        <?php if (!isset($status) || empty($status)): ?>
            <div class="id-selector">
                <h2>Selectează Statusul de Șters</h2>
                <form method="GET" action="/statuses/delete">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="id">ID Status <span class="required">*</span></label>
                            <input type="number" id="id" name="id" required min="1"
                                   value="<?= isset($_GET['id']) ? htmlspecialchars($_GET['id']) : '' ?>"
                                   placeholder="Introdu ID-ul statusului de șters">
                            <div class="help-text">Introdu ID-ul statusului pe care vrei să îl ștergi definitiv</div>
                        </div>
                        <div class="form-group" style="display: flex; align-items: end;">
                            <button type="submit" class="btn btn-danger">Caută pentru Ștergere</button>
                        </div>
                    </div>
                </form>

                <!-- Quick Links to Existing Statuses -->
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
                                <a href="/statuses/delete?id=<?= $stat['id'] ?>" 
                                   class="btn btn-secondary" 
                                   style="padding: 8px 12px; font-size: 14px;">
                                    ID: <?= $stat['id'] ?> - <?= htmlspecialchars(substr($stat['nume'], 0, 20)) ?><?= strlen($stat['nume']) > 20 ? '...' : '' ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <p style="margin-top: 15px; font-size: 14px; color: #666;">
                             <em>Click pe orice status pentru a-l șterge</em>
                        </p>
                    </div>
                <?php 
                    endif;
                } catch (Exception $e) {
                    // Ignore error if we can't load statuses
                }
                ?>
            </div>

        <?php else: ?>
            <!-- Step 2: Delete the Status -->
            
            <div class="status-info-box">
                <h2>Informații Status</h2>
                <div class="status-details">
                    <div class="detail-item">
                        <span class="detail-label">ID Status:</span>
                        <span class="detail-value"><?= htmlspecialchars($status['id']) ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">Nume Status:</span>
                        <span class="detail-value"><?= htmlspecialchars($status['nume']) ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">Data Start:</span>
                        <span class="detail-value">
                            <?= !empty($status['data_start']) ? date('d.m.Y H:i', strtotime($status['data_start'])) : 'Necunoscută' ?>
                        </span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">Data Finalizare:</span>
                        <span class="detail-value">
                            <?php if (!empty($status['data_finalizare'])): ?>
                                <?= date('d.m.Y H:i', strtotime($status['data_finalizare'])) ?>
                                <br><small style="color: #28a745;">Completat</small>
                            <?php else: ?>
                                <span style="color: #ffc107;">În progres...</span>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>

                <?php if (!empty($status['nota_aditionala'])): ?>
                    <div class="detail-item" style="margin-top: 15px;">
                        <span class="detail-label">Notă Adițională:</span>
                        <span class="detail-value"><?= nl2br(htmlspecialchars($status['nota_aditionala'])) ?></span>
                    </div>
                <?php endif; ?>

                <!-- Option to change ID -->
                <p style="margin-top: 15px;">
                    <a href="/statuses/delete" class="btn btn-secondary" style="padding: 8px 12px; font-size: 14px;">
                        Șterge alt status
                    </a>
                </p>
            </div>

            <?php 
            $projectCount = isset($status['projects_count']) ? $status['projects_count'] : 0;
            if ($projectCount > 0): 
            ?>
                <!-- Cannot delete - projects are using this status -->
                <div class="cannot-delete">
                    <h3>Nu se poate șterge statusul</h3>
                    <p>
                        Acest status este folosit de <strong><?= $projectCount ?></strong> 
                        proiect<?= $projectCount > 1 ? 'e' : '' ?> și nu poate fi șters.
                    </p>
                    <p>Pentru a șterge acest status, mai întâi trebuie să:</p>
                    <ul>
                        <li>Modifici statusul proiectelor care îl folosesc</li>
                        <li>Sau ștergi proiectele care îl folosesc</li>
                    </ul>
                </div>

                <div class="actions">
                    <a href="/views/projects?status=<?= $status['id'] ?>" class="btn btn-secondary">
                        Vezi proiectele care folosesc acest status
                    </a>
                    <a href="/statuses/edit?id=<?= $status['id'] ?>" class="btn btn-secondary">
                        Editează statusul
                    </a>
                    <a href="/statuses/delete" class="btn btn-secondary">
                        Șterge alt status
                    </a>
                    <a href="/views/statuses" class="btn btn-secondary">
                        ← Înapoi la statusuri
                    </a>
                </div>

            <?php else: ?>
                <!-- Can delete - no projects using this status -->
                <div class="warning-box">
                    <div class="warning-title">ATENȚIE: Ștergere status</div>
                    <div class="warning-text">
                        Ești pe cale să ștergi permanent statusul <strong>"<?= htmlspecialchars($status['nume']) ?>"</strong>.
                        <br><br>
                        Această acțiune va șterge definitiv statusul și nu poate fi anulată.
                        <br><br>
                        <strong>Statusul poate fi șters în siguranță</strong> - nu este folosit de niciun proiect.
                    </div>
                </div>

                <form method="POST" action="/statuses/destroy" id="deleteForm">
                    <!-- Hidden ID field -->
                    <input type="hidden" name="id" value="<?= $status['id'] ?>">
                    
                    <div class="form-group">
                        <label style="font-weight: normal;">
                            <input type="checkbox" name="confirm_delete" required style="margin-right: 10px;"> 
                            Confirm că vreau să șterg acest status și înțeleg că acțiunea este ireversibilă
                        </label>
                    </div>

                    <div class="form-group">
                        <label style="font-weight: normal;">
                            <input type="checkbox" name="double_confirm" required style="margin-right: 10px;"> 
                            Sunt absolut sigur de această decizie
                        </label>
                    </div>

                    <div class="actions">
                        <button type="submit" class="btn btn-danger">
                            Șterge statusul definitiv
                        </button>
                        
                        <a href="/statuses/edit?id=<?= $status['id'] ?>" class="btn btn-secondary">
                            Editează în schimb
                        </a>
                        
                        <a href="/statuses/delete" class="btn btn-secondary">
                            Șterge alt status
                        </a>
                        
                        <a href="/views/statuses" class="btn btn-secondary">
                            ← Anulează
                        </a>
                    </div>
                </form>

            <?php endif; ?>

        <?php endif; ?>
    </div>

    <script>
        // Confirmation before submit (only if deletion is allowed)
        <?php if (isset($status) && $projectCount == 0): ?>
        document.getElementById('deleteForm').addEventListener('submit', function(e) {
            const statusName = "<?= htmlspecialchars($status['nume']) ?>";
            
            if (!confirm(`Ești absolut sigur că vrei să ștergi statusul "${statusName}"?\n\nAceastă acțiune NU poate fi anulată!`)) {
                e.preventDefault();
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>