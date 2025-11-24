<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vizualizează Toate Proiectele - Casa de Producție</title>
    <style>
        .container { max-width: 1000px; margin: 0 auto; padding: 20px; }
        .back-link { color: #007bff; text-decoration: none; margin-bottom: 20px; display: inline-block; }
        .back-link:hover { text-decoration: underline; }
        .project-item { border: 1px solid #ddd; border-radius: 8px; margin-bottom: 15px; background: white; }
        .project-header { padding: 15px 20px; cursor: pointer; background: #f8f9fa; border-radius: 8px 8px 0 0; display: flex; justify-content: space-between; align-items: center; }
        .project-header:hover { background: #e9ecef; }
        .project-title { font-size: 1.3em; font-weight: bold; color: #333; }
        .project-meta { color: #666; font-size: 0.9em; }
        .dropdown-icon { font-size: 1.2em; transition: transform 0.3s ease; color: #666; }
        .dropdown-icon.rotated { transform: rotate(180deg); }
        .project-details { padding: 0 20px 20px 20px; display: none; background: #fff; border-radius: 0 0 8px 8px; }
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 15px; }
        .detail-item { padding: 10px; background: #f8f9fa; border-radius: 4px; }
        .detail-label { font-weight: bold; color: #333; display: block; margin-bottom: 5px; }
        .detail-value { color: #666; }
        .description-full { grid-column: 1 / -1; }
        .no-projects { text-align: center; padding: 40px; color: #666; }
        .projects-count { color: #666; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="/views/projects" class="back-link">← Înapoi la meniul proiectelor</a>
        
        <h2>Toate Proiectele</h2>
        
        <?php if (!empty($projects)): ?>
            <div class="projects-count">
                <strong>Total: <?= count($projects) ?> proiecte găsite</strong>
            </div>
            
            <?php foreach ($projects as $project): ?>
                <div class="project-item">
                    <div class="project-header" onclick="toggleDetails(<?= $project['id'] ?>)">
                        <div>
                            <div class="project-title"><?= htmlspecialchars($project['title']) ?></div>
                            <div class="project-meta">
                                ID: <?= $project['id'] ?> | 
                                Tip: <?= ucfirst(htmlspecialchars($project['tip'])) ?> |
                                Buget: <?php if ($project['buget'] > 0): ?>
                                    <?= number_format($project['buget'], 2) ?> RON
                                <?php else: ?>
                                    Nu este specificat
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="dropdown-icon" id="icon-<?= $project['id'] ?>">▼</div>
                    </div>
                    
                    <div class="project-details" id="details-<?= $project['id'] ?>">
                        <div class="detail-grid">
                            <div class="detail-item">
                                <span class="detail-label">ID Proiect:</span>
                                <span class="detail-value"><?= htmlspecialchars($project['id']) ?></span>
                            </div>
                            
                            <div class="detail-item">
                                <span class="detail-label">Tip Proiect:</span>
                                <span class="detail-value"><?= ucfirst(htmlspecialchars($project['tip'])) ?></span>
                            </div>
                            
                            <div class="detail-item">
                                <span class="detail-label">Buget:</span>
                                <span class="detail-value">
                                    <?php if ($project['buget'] > 0): ?>
                                        <?= number_format($project['buget'], 2) ?> RON
                                    <?php else: ?>
                                        Nu este specificat
                                    <?php endif; ?>
                                </span>
                            </div>
                            
                            <?php if (!empty($project['durata_derulare'])): ?>
                                <div class="detail-item">
                                    <span class="detail-label">Durată:</span>
                                    <span class="detail-value"><?= htmlspecialchars($project['durata_derulare']) ?> minute</span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($project['status_name'])): ?>
                                <div class="detail-item">
                                    <span class="detail-label">Status:</span>
                                    <span class="detail-value"><?= htmlspecialchars($project['status_name']) ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($project['prenume']) && !empty($project['nume_familie'])): ?>
                                <div class="detail-item">
                                    <span class="detail-label">Contribuitor:</span>
                                    <span class="detail-value"><?= htmlspecialchars($project['prenume'] . ' ' . $project['nume_familie']) ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($project['poster_url'])): ?>
                                <div class="detail-item">
                                    <span class="detail-label">Poster:</span>
                                    <span class="detail-value">
                                        <a href="<?= htmlspecialchars($project['poster_url']) ?>" target="_blank" style="color: #007bff;">Vezi poster</a>
                                    </span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($project['descriere'])): ?>
                                <div class="detail-item description-full">
                                    <span class="detail-label">Descriere:</span>
                                    <span class="detail-value"><?= nl2br(htmlspecialchars($project['descriere'])) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
        <?php else: ?>
            <div class="no-projects">
                <h3>Nu există proiecte</h3>
                <p>Nu au fost găsite proiecte în baza de date.</p>
                <a href="/projects/create" style="color: #007bff;">Creează primul proiect</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function toggleDetails(projectId) {
            const details = document.getElementById('details-' + projectId);
            const icon = document.getElementById('icon-' + projectId);
            
            if (details.style.display === 'none' || details.style.display === '') {
                details.style.display = 'block';
                icon.classList.add('rotated');
            } else {
                details.style.display = 'none';
                icon.classList.remove('rotated');
            }
        }
    </script>
</body>
</html>