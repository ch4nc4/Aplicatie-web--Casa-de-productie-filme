<?php

// Get data needed for form dropdowns
require_once __DIR__ . '/../../Models/StatusProject.php';
require_once __DIR__ . '/../../Models/User.php';

$statusModel = new StatusProject();
$userModel = new User();

$statuses = $statusModel->getAll();
$users = $userModel->getAll();

session_start();
$userId = $_SESSION['user_id'] ?? null;
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adaugă Proiect - Casa de Producție Filme</title>
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
        <a href="/views/projects" class="back-link">← Înapoi la proiecte</a>
        
        <h1>Adaugă Proiect Nou</h1>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (empty($statuses) && empty($users)): ?>
            <div class="warning">
                <strong>Atenție!</strong> Nu există statusuri și contribuitori în sistem. Pentru a crea un proiect, ai nevoie de cel puțin un status și un contribuitor.
            </div>
        <?php elseif (empty($statuses)): ?>
            <div class="warning">
                <strong>Atenție!</strong> Nu există statusuri în sistem. Trebuie să adaugi cel puțin un status pentru a crea un proiect.
            </div>
        <?php elseif (empty($users)): ?>
            <div class="warning">
                <strong>Atenție!</strong> Nu există contribuitori în sistem. Trebuie să adaugi cel puțin un utilizator pentru a-l asigna ca contribuitor.
            </div>
        <?php endif; ?>
        
        <form method="POST" action="/projects/store">
            <?php echo csrf_token_field(); ?>
            <div class="form-group">
                <label for="title">Titlu Proiect *</label>
                <input type="text" id="title" name="title" required 
                       value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="tip">Tip Proiect *</label>
                <select id="tip" name="tip" required>
                    <option value="">Selectează tipul</option>
                    <option value="film" <?= (isset($_POST['tip']) && $_POST['tip'] === 'film') ? 'selected' : '' ?>>Film</option>
                    <option value="scurtmetraj" <?= (isset($_POST['tip']) && $_POST['tip'] === 'scurtmetraj') ? 'selected' : '' ?>>Scurtmetraj</option>
                    <option value="documentar" <?= (isset($_POST['tip']) && $_POST['tip'] === 'documentar') ? 'selected' : '' ?>>Documentar</option>
                    <option value="reclama" <?= (isset($_POST['tip']) && $_POST['tip'] === 'reclama') ? 'selected' : '' ?>>Reclamă</option>
                    <option value="videoclip" <?= (isset($_POST['tip']) && $_POST['tip'] === 'videoclip') ? 'selected' : '' ?>>Videoclip</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="descriere">Descriere/Viziune Artistică</label>
                <textarea id="descriere" name="descriere" placeholder="Descrierea proiectului și viziunea artistică..."><?= isset($_POST['descriere']) ? htmlspecialchars($_POST['descriere']) : '' ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="buget">Buget Aproximat (RON)</label>
                <input type="number" id="buget" name="buget" min="0" step="0.01" 
                       value="<?= isset($_POST['buget']) ? htmlspecialchars($_POST['buget']) : '0' ?>">
            </div>
            
            <div class="form-group">
                <label for="durata_derulare">Durată Aproximativă (minute)</label>
                <input type="number" id="durata_derulare" name="durata_derulare" min="1" 
                       value="<?= isset($_POST['durata_derulare']) ? htmlspecialchars($_POST['durata_derulare']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="poster_url">URL Poster (opțional)</label>
                <input type="url" id="poster_url" name="poster_url" 
                       value="<?= isset($_POST['poster_url']) ? htmlspecialchars($_POST['poster_url']) : '' ?>"
                       placeholder="https://...">
            </div>
            
            <div class="form-group">
                <label for="id_status">Status Inițial *</label>
                <?php if (empty($statuses)): ?>
                    <div class="empty-data">
                        <p><strong>Nu există statusuri în sistem</strong></p>
                        <p>Trebuie să adaugi cel puțin un status pentru a crea un proiect.</p>
                        <div class="add-option">
                            <a href="/statuses/create" class="btn btn-success">Adaugă primul status</a>
                            <a href="/views/statuses" class="btn btn-secondary">Vezi toate statusurile</a>
                        </div>
                    </div>
                    <select id="id_status" name="id_status" required disabled>
                        <option value="">Nu există statusuri disponibile</option>
                    </select>
                <?php else: ?>
                    <select id="id_status" name="id_status" required>
                        <option value="">Selectează statusul</option>
                        <?php foreach ($statuses as $status): ?>
                            <option value="<?= $status['id'] ?>" 
                                    <?= (isset($_POST['id_status']) && $_POST['id_status'] == $status['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($status['nume']) ?>
                                <?php if (!empty($status['data_start'])): ?>
                                    (Start: <?= date('d.m.Y', strtotime($status['data_start'])) ?>)
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="quick-add">
                        <h4>Acțiuni rapide pentru statusuri:</h4>
                        <a href="/statuses/create" class="btn btn-success" style="font-size: 14px; padding: 6px 12px;">Adaugă status nou</a>
                        <a href="/views/statuses" class="btn btn-secondary" style="font-size: 14px; padding: 6px 12px;">Gestionează statusuri</a>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="contribuitor">Contribuitor Principal *</label>
                <?php if (!$userId): ?>
                    <div class="warning">Trebuie să fii autentificat pentru a crea un proiect.</div>
                <?php else: ?>
                    <input type="hidden" name="contribuitor" value="<?= $userId ?>">
                    <div>
                        <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'Utilizator') ?></strong>
                        (<?= htmlspecialchars($_SESSION['user_email'] ?? '') ?>)
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <?php if (!empty($statuses) && !empty($users)): ?>
                    <button type="submit" class="btn">Creează Proiect</button>
                <?php else: ?>
                    <button type="submit" class="btn" disabled style="background-color: #6c757d; cursor: not-allowed;">
                        Nu se poate crea proiectul (lipsesc date)
                    </button>
                <?php endif; ?>
                <a href="/views/projects" class="btn btn-secondary" style="margin-left: 10px;">Anulează</a>
            </div>

            <?php if (empty($statuses) || empty($users)): ?>
                <div class="warning">
                    <strong>Pentru a finaliza crearea proiectului:</strong>
                    <ul style="margin: 10px 0 0 20px;">
                        <?php if (empty($statuses)): ?>
                            <li>Adaugă cel puțin un status (ex: "În dezvoltare", "Pre-producție", etc.)</li>
                        <?php endif; ?>
                        <?php if (empty($users)): ?>
                            <li>Înregistrează cel puțin un utilizator pentru a-l asigna ca contribuitor</li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <script>
        window.addEventListener('focus', function() {
            setTimeout(() => {
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.get('refresh') === '1') {
                    location.reload();
                }
            }, 500);
        });

        document.querySelector('form').addEventListener('submit', function(e) {
            const hasStatuses = <?= !empty($statuses) ? 'true' : 'false' ?>;
            const hasUsers = <?= !empty($users) ? 'true' : 'false' ?>;
            
            if (!hasStatuses || !hasUsers) {
                e.preventDefault();
                alert('Nu se poate crea proiectul!\n\n' +
                      (!hasStatuses ? '• Adaugă cel puțin un status\n' : '') +
                      (!hasUsers ? '• Adaugă cel puțin un utilizator\n' : ''));
            }
        });
    </script>
</body>
</html>