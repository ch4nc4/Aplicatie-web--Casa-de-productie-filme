<?php
    session_start();
    $userId = $_SESSION['user_id'] ?? null;
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Șterge Proiect<?= isset($project) ? ' - ' . htmlspecialchars($project['titlu']) : '' ?></title>
    <style>
        body {
            font-f            <form method="POST" action="/projects/destroy" id="deleteForm">
                <?php echo csrf_token_field(); ?>
                <input type="hidden" name="project_id" value="<?= $project['id'] ?>">ily: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .container { 
            max-width: 800px; 
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
        .warning-title { 
            color: #721c24; 
            font-weight: bold; 
            margin-bottom: 10px;
            font-size: 1.2em;
        }
        .warning-text { 
            color: #721c24; 
        }
        .project-info-box { 
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
        .project-details {
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
        .detail-item.full-width {
            grid-column: 1 / -1;
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
        .search-type-selector {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .type-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            color: white;
            text-transform: uppercase;
        }
        .type-film { background-color: #007bff; }
        .type-scurtmetraj { background-color: #28a745; }
        .type-documentar { background-color: #ffc107; color: #212529; }
        .type-reclama { background-color: #dc3545; }
        .type-videoclip { background-color: #6f42c1; }
    </style>
</head>
<body>
    <div class="container">
        <a href="/views/projects" class="back-link">← Înapoi la proiecte</a>
        
        <h1>Șterge Proiect</h1>

        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if (!isset($project) || empty($project)): ?>
            <div class="id-selector">
                <h2>Selectează Proiectul de Șters</h2>
                
                <!-- <div class="search-type-selector">
                    <label>🔍 Caută proiectul după:</label>
                    <select id="search_type" onchange="updateSearchMode()">
                        <option value="id">ID Proiect</option>
                        <option value="title">Titlu Proiect</option>
                    </select>
                </div>

                <form method="GET" action="/projects/delete" id="searchForm">
                    <input type="hidden" name="search_by" id="search_by_hidden" value="id">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="project_identifier">
                                <span id="search_label">ID Proiect</span> <span class="required">*</span>
                            </label>
                            <input type="number" id="project_identifier" name="project_identifier" required min="1"
                                   value="<?= isset($_GET['project_identifier']) ? htmlspecialchars($_GET['project_identifier']) : '' ?>"
                                   placeholder="Introdu ID-ul proiectului de șters">
                            <div class="help-text" id="search_help">
                                Introdu ID-ul proiectului pe care vrei să îl ștergi definitiv
                            </div>
                        </div>
                        <div class="form-group" style="display: flex; align-items: end;">
                            <button type="submit" class="btn btn-danger">🔍 Caută pentru Ștergere</button>
                        </div>
                    </div>
                </form> -->

                <?php 
                try {
                    require_once __DIR__ . '/../../Models/Project.php';
                    $projectModel = new Project();
                    $allProjects = $projectModel->getAll();
                    if (!empty($allProjects)):
                        $userProjects = array_filter($allProjects, function($proj) use ($userId) {
                         return $proj['id_contribuitor'] == $userId;
                    });
                ?>
                    <div style="margin-top: 20px;">
                        <h3>🎬 Proiecte Disponibile:</h3>
                        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                            <?php foreach ($userProjects as $proj): ?>
                                <a href="/projects/delete?search_by=id&project_identifier=<?= $proj['id'] ?>" 
                                   class="btn btn-secondary" 
                                   style="padding: 8px 12px; font-size: 14px; text-align: left;">
                                    <div>
                                        <strong>ID: <?= $proj['id'] ?></strong>
                                        <span class="type-badge type-<?= $proj['tip'] ?>"><?= strtoupper($proj['tip']) ?></span>
                                    </div>
                                    <div style="font-size: 12px; margin-top: 2px;">
                                        <?= htmlspecialchars(substr($proj['titlu'], 0, 25)) ?><?= strlen($proj['titlu']) > 25 ? '...' : '' ?>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <p style="margin-top: 15px; font-size: 14px; color: #666;">
                            <em>Click pe orice proiect pentru a-l șterge</em>
                        </p>
                    </div>
                <?php 
                    endif;
                } catch (Exception $e) {
                    // Ignore error if we can't load projects
                }
                ?>
            </div>

        <?php else: ?>
            
            <div class="project-info-box">
                <h2>Informații Proiect</h2>
                <div class="project-details">
                    <div class="detail-item">
                        <span class="detail-label">ID Proiect:</span>
                        <span class="detail-value"><?= htmlspecialchars($project['id']) ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">Tip:</span>
                        <span class="detail-value">
                            <span class="type-badge type-<?= $project['tip'] ?>"><?= strtoupper($project['tip']) ?></span>
                        </span>
                    </div>
                    
                    <div class="detail-item full-width">
                        <span class="detail-label">Titlu:</span>
                        <span class="detail-value"><strong><?= htmlspecialchars($project['titlu']) ?></strong></span>
                    </div>
                    
                    <?php if (!empty($project['descriere_viziune'])): ?>
                    <div class="detail-item full-width">
                        <span class="detail-label">Descriere:</span>
                        <span class="detail-value"><?= nl2br(htmlspecialchars(substr($project['descriere_viziune'], 0, 200))) ?><?= strlen($project['descriere_viziune']) > 200 ? '...' : '' ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="detail-item">
                        <span class="detail-label">Buget:</span>
                        <span class="detail-value">
                            <?= !empty($project['buget_aproximativ']) ? number_format($project['buget_aproximativ'], 2) . ' RON' : 'Nu este specificat' ?>
                        </span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">Durată:</span>
                        <span class="detail-value">
                            <?= !empty($project['durata_derulare']) ? $project['durata_derulare'] . ' minute' : 'Nu este specificată' ?>
                        </span>
                    </div>

                    <?php if (!empty($project['status_nume'])): ?>
                    <div class="detail-item">
                        <span class="detail-label">Status Curent:</span>
                        <span class="detail-value">
                            <strong><?= htmlspecialchars($project['status_nume']) ?></strong>
                        </span>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($project['contribuitor_nume'])): ?>
                    <div class="detail-item">
                        <span class="detail-label">Contribuitor Principal:</span>
                        <span class="detail-value">
                            <?= htmlspecialchars($project['contribuitor_nume']) ?>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>

                <p style="margin-top: 15px;">
                    <a href="/projects/delete" class="btn btn-secondary" style="padding: 8px 12px; font-size: 14px;">
                        Șterge alt proiect
                    </a>
                </p>
            </div>

            <div class="warning-box">
                <div class="warning-title">ATENȚIE: Ștergere proiect</div>
                <div class="warning-text">
                    Ești pe cale să ștergi permanent proiectul <strong>"<?= htmlspecialchars($project['titlu']) ?>"</strong>.
                    <br><br>
                    Această acțiune va șterge definitiv:
                    <ul style="margin: 10px 0 10px 20px;">
                        <li>Toate informațiile despre proiect</li>
                        <li>Datele de buget și durată</li>
                        <li>Descrierea și viziunea artistică</li>
                        <li>Asocierile cu contribuitorii</li>
                        <li>Statusul curent</li>
                    </ul>
                    <br>
                    <strong>Acțiunea NU poate fi anulată!</strong>
                </div>
            </div>

            <form method="POST" action="/projects/destroy" id="deleteForm">
                <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
                
                <div class="form-group">
                    <label style="font-weight: normal;">
                        <input type="checkbox" name="confirm_delete" required style="margin-right: 10px;"> 
                        Confirm că vreau să șterg acest proiect și înțeleg că acțiunea este ireversibilă
                    </label>
                </div>

                <div class="form-group">
                    <label style="font-weight: normal;">
                        <input type="checkbox" name="double_confirm" required style="margin-right: 10px;"> 
                        Sunt absolut sigur de această decizie și accept consecințele
                    </label>
                </div>

                <div class="form-group">
                    <label style="font-weight: normal;">
                        <input type="checkbox" name="final_confirm" required style="margin-right: 10px;"> 
                        Am înțeles că această acțiune va șterge permanent proiectul "<?= htmlspecialchars($project['titlu']) ?>"
                    </label>
                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-danger">
                        Șterge proiectul definitiv
                    </button>
                    
                    <a href="/projects/edit?id=<?= $project['id'] ?>" class="btn btn-secondary">
                        Editează în schimb
                    </a>
                    
                    <a href="/projects/delete" class="btn btn-secondary">
                        Șterge alt proiect
                    </a>
                    
                    <a href="/views/projects" class="btn btn-secondary">
                        ← Anulează
                    </a>
                </div>
            </form>

        <?php endif; ?>
    </div>

    <script>
        function updateSearchMode() {
            const searchType = document.getElementById('search_type').value;
            const identifier = document.getElementById('project_identifier');
            const label = document.getElementById('search_label');
            const help = document.getElementById('search_help');
            const hiddenInput = document.getElementById('search_by_hidden');

            hiddenInput.value = searchType;

            if (searchType === 'id') {
                identifier.type = 'number';
                identifier.placeholder = 'Introdu ID-ul proiectului (ex: 1, 2, 3...)';
                identifier.min = '1';
                label.textContent = 'ID Proiect';
                help.textContent = 'Introdu ID-ul proiectului pe care vrei să îl ștergi definitiv';
            } else {
                identifier.type = 'text';
                identifier.placeholder = 'Introdu titlul exact al proiectului';
                identifier.removeAttribute('min');
                label.textContent = 'Titlu Proiect';
                help.textContent = 'Introdu titlul exact al proiectului pe care vrei să îl ștergi definitiv';
            }

            identifier.value = '';
        }

        <?php if (isset($project)): ?>
        document.getElementById('deleteForm').addEventListener('submit', function(e) {
            const projectTitle = "<?= htmlspecialchars($project['titlu']) ?>";
            
            if (!confirm(`Ești absolut sigur că vrei să ștergi proiectul "${projectTitle}"?\n\nAceastă acțiune NU poate fi anulată!\n\nToate datele vor fi pierdute definitiv!`)) {
                e.preventDefault();
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>