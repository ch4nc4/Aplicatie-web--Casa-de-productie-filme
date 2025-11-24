<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editează Proiect - Casa de Producție Filme</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .container { 
            max-width: 900px; 
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
        .warning {
            color: #856404;
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
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
        .project-info {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .project-info h3 {
            margin: 0 0 10px 0;
            color: #495057;
        }
        .id-selector {
            background-color: #f8f9fa;
            border: 2px solid #007bff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .type-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            color: white;
            text-transform: uppercase;
            margin-left: 10px;
        }
        .type-film { background-color: #007bff; }
        .type-scurtmetraj { background-color: #28a745; }
        .type-documentar { background-color: #ffc107; color: #212529; }
        .type-reclama { background-color: #dc3545; }
        .type-videoclip { background-color: #6f42c1; }
        .status-indicator {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }
        .search-type-selector {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/views/projects" class="back-link">← Înapoi la proiecte</a>
        
        <h1>Editează Proiect</h1>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if (!isset($project) || empty($project)): ?>
            <div class="id-selector">
                <h2>Selectează Proiectul de Editat</h2>
                <?php 
                try {
                    require_once __DIR__ . '/../../Models/Project.php';
                    $projectModel = new Project();
                    $allProjects = $projectModel->getAll();
                    if (!empty($allProjects)): 
                ?>
                    <div style="margin-top: 20px;">
                        <h3>Proiecte Disponibile:</h3>
                        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                            <?php foreach ($allProjects as $proj): ?>
                                <a href="/projects/edit?project_identifier=<?= $proj['id'] ?>" 
                                   class="btn btn-secondary" 
                                   style="padding: 8px 12px; font-size: 14px; text-align: left;">
                                    <div>
                                        <strong>ID: <?= $proj['id'] ?></strong>
                                        <span class="type-badge type-<?= $proj['tip'] ?>"><?= strtoupper($proj['tip']) ?></span>
                                    </div>
                                    <div style="font-size: 12px; margin-top: 2px;">
                                        <?= htmlspecialchars(substr($proj['title'], 0, 25)) ?><?= strlen($proj['title']) > 25 ? '...' : '' ?>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php 
                    endif;
                } catch (Exception $e) {
                    // Ignore error if we can't load projects
                }
                ?>
            </div>

        <?php else: ?>
            <div class="project-info">
                <h3>Informații Proiect</h3>
                <p><strong>ID:</strong> <?= htmlspecialchars($project['id']) ?></p>
                <p><strong>Titlu Curent:</strong> <?= htmlspecialchars($project['title']) ?></p>
                <p><strong>Tip:</strong> 
                    <span class="type-badge type-<?= $project['tip'] ?>"><?= strtoupper($project['tip']) ?></span>
                </p>
                <?php if (!empty($project['status_nume'])): ?>
                <p><strong>Status:</strong> 
                    <span class="status-indicator" style="background-color: #d1ecf1; color: #0c5460;">
                        <?= htmlspecialchars($project['status_nume']) ?>
                    </span>
                </p>
                <?php endif; ?>
                
                <p>
                    <a href="/projects/edit" class="btn btn-secondary" style="padding: 8px 12px; font-size: 14px;">
                        Editează alt proiect
                    </a>
                </p>
            </div>
            
            <form method="POST" action="/projects/update">
                <input type="hidden" name="id" value="<?= $project['id'] ?>">
                
                <div class="form-group">
                    <label for="titlu">Titlu Proiect <span class="required">*</span></label>
                    <input type="text" id="titlu" name="titlu" required maxlength="200"
                           value="<?= htmlspecialchars($project['title'] ?? '') ?>"
                           placeholder="Ex: Povestea din spatele camerelor, Documentar România, etc.">
                    <div class="help-text">Titlul proiectului (maxim 200 caractere)</div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="tip">Tip Proiect <span class="required">*</span></label>
                        <select id="tip" name="tip" required>
                            <option value="">Selectează tipul</option>
                            <option value="film" <?= (isset($project['tip']) && $project['tip'] === 'film') ? 'selected' : '' ?>>Film</option>
                            <option value="scurtmetraj" <?= (isset($project['tip']) && $project['tip'] === 'scurtmetraj') ? 'selected' : '' ?>>Scurtmetraj</option>
                            <option value="documentar" <?= (isset($project['tip']) && $project['tip'] === 'documentar') ? 'selected' : '' ?>>Documentar</option>
                            <option value="reclama" <?= (isset($project['tip']) && $project['tip'] === 'reclama') ? 'selected' : '' ?>>Reclamă</option>
                            <option value="videoclip" <?= (isset($project['tip']) && $project['tip'] === 'videoclip') ? 'selected' : '' ?>>Videoclip</option>
                        </select>
                        <div class="help-text">Categoria proiectului</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="durata_derulare">Durată (minute)</label>
                        <input type="number" id="durata_derulare" name="durata_derulare" min="1"
                               value="<?= htmlspecialchars($project['durata_derulare'] ?? '') ?>"
                               placeholder="Ex: 90, 15, 120">
                        <div class="help-text">Durata aproximativă în minute</div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="descriere_viziune">Descriere/Viziune Artistică</label>
                    <textarea id="descriere_viziune" name="descriere_viziune"
                              placeholder="Descrierea proiectului, viziunea artistică, conceptul general..."><?= htmlspecialchars($project['descriere_viziune'] ?? '') ?></textarea>
                    <div class="help-text">Detalii despre proiect, viziunea artistică (opțional)</div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="buget_aproximativ">Buget Aproximativ (RON)</label>
                        <input type="number" id="buget_aproximativ" name="buget_aproximativ" min="0" step="0.01"
                               value="<?= htmlspecialchars($project['buget_aproximativ'] ?? '') ?>"
                               placeholder="Ex: 50000.00">
                        <div class="help-text">Bugetul estimat pentru proiect</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="poster_url">URL Poster</label>
                        <input type="url" id="poster_url" name="poster_url"
                               value="<?= htmlspecialchars($project['poster_url'] ?? '') ?>"
                               placeholder="https://example.com/poster.jpg">
                        <div class="help-text">Link către posterul proiectului (opțional)</div>
                    </div>
                </div>
                
                <?php if (!empty($statuses)): ?>
                <div class="form-group">
                    <label for="id_status_proiect">Status Proiect</label>
                    <select id="id_status_proiect" name="id_status_proiect">
                        <option value="">Selectează statusul</option>
                        <?php foreach ($statuses as $status): ?>
                            <option value="<?= $status['id'] ?>" 
                                    <?= (isset($project['id_status_proiect']) && $project['id_status_proiect'] == $status['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($status['nume']) ?>
                                <?php if (!empty($status['data_start'])): ?>
                                    (Start: <?= date('d.m.Y', strtotime($status['data_start'])) ?>)
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="help-text">Statusul actual al proiectului</div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($users)): ?>
                <div class="form-group">
                    <label for="id_contribuitor_principal">Contribuitor Principal</label>
                    <select id="id_contribuitor_principal" name="id_contribuitor_principal">
                        <option value="">Selectează contributorul</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user['id'] ?>" 
                                    <?= (isset($project['id_contribuitor_principal']) && $project['id_contribuitor_principal'] == $user['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user['prenume'] . ' ' . $user['nume_familie']) ?>
                                <?php if (!empty($user['email'])): ?>
                                    (<?= htmlspecialchars($user['email']) ?>)
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="help-text">Responsabilul principal pentru proiect</div>
                </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <button type="submit" class="btn">Salvează Modificările</button>
                    <a href="/views/projects" class="btn btn-secondary" style="margin-left: 10px;">Anulează</a>
                    <a href="/projects/edit" class="btn btn-secondary" style="margin-left: 10px;">Editează Alt Proiect</a>
                    
                    <a href="/projects/delete?project_identifier=<?= $project['id'] ?>" class="btn" 
                       style="background-color: #dc3545; margin-left: 10px;"
                       onclick="return confirm('Ești sigur că vrei să ștergi acest proiect?')">
                        Șterge Proiect
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
                help.textContent = 'Introdu ID-ul proiectului pe care vrei să îl editezi';
            } else {
                identifier.type = 'text';
                identifier.placeholder = 'Introdu titlul exact al proiectului';
                identifier.removeAttribute('min');
                label.textContent = 'Titlu Proiect';
                help.textContent = 'Introdu titlul exact al proiectului pe care vrei să îl editezi';
            }

            identifier.value = '';
        }

        // Form validation
        document.querySelector('form[action="/projects/update"]')?.addEventListener('submit', function(e) {
            const titlu = document.getElementById('titlu').value.trim();
            const tip = document.getElementById('tip').value;
            
            if (!titlu || !tip) {
                e.preventDefault();
                alert('Te rugăm să completezi toate câmpurile obligatorii (*)');
                return false;
            }
        });
    </script>
</body>
</html>