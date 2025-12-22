<?php

require_once __DIR__ . '/../../Models/User.php';
require_once __DIR__ . '/../../Models/Project.php';

$userModel = new User();
$projectModel = new Project();

$users = $userModel->getAll();
$projects = $projectModel->getAll();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adaugă în Watchlist - Casa de Producție Filme</title>
    <style>
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .btn { padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background-color: #0056b3; }
        .btn-secondary { background-color: #6c757d; }
        .btn-secondary:hover { background-color: #545b62; }
        .error { color: red; margin-top: 10px; padding: 10px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; }
        .warning { color: #856404; background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .back-link { display: inline-block; margin-bottom: 20px; color: #007bff; text-decoration: none; }
        .empty-data { background-color: #f8f9fa; border: 2px dashed #dee2e6; padding: 20px; border-radius: 8px; margin: 10px 0; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <a href="/views/watchlist" class="back-link">← Înapoi la watchlist</a>
        <h1>Adaugă Element în Watchlist</h1>

        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (empty($users) || empty($projects)): ?>
            <div class="warning">
                <strong>Atenție!</strong>
                <?php if (empty($users)): ?>
                    Nu există utilizatori în sistem. Trebuie să adaugi cel puțin un utilizator.
                <?php endif; ?>
                <?php if (empty($projects)): ?>
                    Nu există proiecte în sistem. Trebuie să adaugi cel puțin un proiect.
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/watchlist/add">
            <?php echo csrf_token_field(); ?>
        
            <div class="form-group">
                <label for="project_id">Proiect *</label>
                <?php if (empty($projects)): ?>
                    <select id="project_id" name="project_id" required disabled>
                        <option value="">Nu există proiecte disponibile</option>
                    </select>
                <?php else: ?>
                    <select id="project_id" name="project_id" required>
                        <option value="">Selectează proiectul</option>
                        <?php foreach ($projects as $project): ?>
                            <option value="<?= $project['id'] ?>">
                                <?= htmlspecialchars($project['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <?php if (!empty($users) && !empty($projects)): ?>
                    <button type="submit" class="btn">Adaugă în Watchlist</button>
                <?php else: ?>
                    <button type="submit" class="btn" disabled style="background-color: #6c757d; cursor: not-allowed;">
                        Nu se poate adăuga (lipsesc date)
                    </button>
                <?php endif; ?>
                <a href="/views/watchlist" class="btn btn-secondary" style="margin-left: 10px;">Anulează</a>
            </div>
        </form>
    </div>