<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vizualizează Toate Statusurile - Casa de Producție</title>
    <style>
        .container { max-width: 1000px; margin: 0 auto; padding: 20px; }
        .back-link { color: #007bff; text-decoration: none; margin-bottom: 20px; display: inline-block; }
        .back-link:hover { text-decoration: underline; }
        .status-item { border: 1px solid #ddd; border-radius: 8px; margin-bottom: 15px; background: white; }
        .status-header { padding: 15px 20px; cursor: pointer; background: #f8f9fa; border-radius: 8px 8px 0 0; display: flex; justify-content: space-between; align-items: center; }
        .status-header:hover { background: #e9ecef; }
        .status-title { font-size: 1.3em; font-weight: bold; color: #333; }
        .status-meta { color: #666; font-size: 0.9em; }
        .dropdown-icon { font-size: 1.2em; transition: transform 0.3s ease; color: #666; }
        .dropdown-icon.rotated { transform: rotate(180deg); }
        .status-details { padding: 0 20px 20px 20px; display: none; background: #fff; border-radius: 0 0 8px 8px; }
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 15px; }
        .detail-item { padding: 10px; background: #f8f9fa; border-radius: 4px; }
        .detail-label { font-weight: bold; color: #333; display: block; margin-bottom: 5px; }
        .detail-value { color: #666; }
        .nota-full { grid-column: 1 / -1; }
        .no-statuses { text-align: center; padding: 40px; color: #666; }
        .statuses-count { color: #666; margin-bottom: 20px; }
        .status-active { border-left: 5px solid #28a745; }
        .status-completed { border-left: 5px solid #17a2b8; }
        .status-pending { border-left: 5px solid #ffc107; }
        .projects-using { color: #007bff; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <a href="/views/statuses" class="back-link">← Înapoi la meniul statusurilor</a>
        
        <h2>Toate Statusurile Proiectelor</h2>
        
        <?php if (!empty($statuses)): ?>
            <div class="statuses-count">
                <strong>Total: <?= count($statuses) ?> statusuri găsite</strong>
            </div>
            
            <?php foreach ($statuses as $status): ?>
                <?php 
                $isCompleted = !empty($status['data_finalizare']); 
                $isActive = !$isCompleted && !empty($status['data_start']) && strtotime($status['data_start']) <= time();
                $isPending = !$isCompleted && (!empty($status['data_start']) && strtotime($status['data_start']) > time());
                
                $statusClass = '';
                if ($isCompleted) $statusClass = 'status-completed';
                elseif ($isActive) $statusClass = 'status-active'; 
                elseif ($isPending) $statusClass = 'status-pending';
                ?>
                
                <div class="status-item <?= $statusClass ?>">
                    <div class="status-header" onclick="toggleDetails(<?= $status['id'] ?>)">
                        <div>
                            <div class="status-title"><?= htmlspecialchars($status['nume']) ?></div>
                            <div class="status-meta">
                                ID: <?= $status['id'] ?> | 
                                <?php if ($isCompleted): ?>
                                    <span style="color: #17a2b8;">✓ Finalizat</span>
                                <?php elseif ($isActive): ?>
                                    <span style="color: #28a745;">● Activ</span>
                                <?php elseif ($isPending): ?>
                                    <span style="color: #ffc107;">⏳ În așteptare</span>
                                <?php else: ?>
                                    <span style="color: #6c757d;">⚫ Nedefinit</span>
                                <?php endif; ?>
                                
                                <?php if (isset($status['projects_count'])): ?>
                                    | <span class="projects-using"><?= $status['projects_count'] ?> proiecte</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="dropdown-icon" id="icon-<?= $status['id'] ?>">▼</div>
                    </div>
                    
                    <div class="status-details" id="details-<?= $status['id'] ?>">
                        <div class="detail-grid">
                            <div class="detail-item">
                                <span class="detail-label">ID Status:</span>
                                <span class="detail-value"><?= htmlspecialchars($status['id']) ?></span>
                            </div>
                            
                            <div class="detail-item">
                                <span class="detail-label">Nume Status:</span>
                                <span class="detail-value"><?= htmlspecialchars($status['nume']) ?></span>
                            </div>
                            
                            <?php if (!empty($status['data_start'])): ?>
                                <div class="detail-item">
                                    <span class="detail-label">Data Start:</span>
                                    <span class="detail-value">
                                        <?= date('d.m.Y H:i', strtotime($status['data_start'])) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($status['data_finalizare'])): ?>
                                <div class="detail-item">
                                    <span class="detail-label">Data Finalizare:</span>
                                    <span class="detail-value">
                                        <?= date('d.m.Y H:i', strtotime($status['data_finalizare'])) ?>
                                        <br><small style="color: #28a745;">✓ Completat</small>
                                    </span>
                                </div>
                            <?php else: ?>
                                <div class="detail-item">
                                    <span class="detail-label">Data Finalizare:</span>
                                    <span class="detail-value" style="color: #ffc107;">În progres...</span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($status['data_start']) && !empty($status['data_finalizare'])): ?>
                                <?php 
                                $start = new DateTime($status['data_start']);
                                $end = new DateTime($status['data_finalizare']);
                                $duration = $start->diff($end);
                                ?>
                                <div class="detail-item">
                                    <span class="detail-label">Durată Totală:</span>
                                    <span class="detail-value">
                                        <?php if ($duration->days > 0): ?>
                                            <?= $duration->days ?> zile
                                        <?php endif; ?>
                                        <?php if ($duration->h > 0): ?>
                                            <?= $duration->h ?> ore
                                        <?php endif; ?>
                                        <?php if ($duration->i > 0): ?>
                                            <?= $duration->i ?> minute
                                        <?php endif; ?>
                                    </span>
                                </div>
                            <?php elseif (!empty($status['data_start'])): ?>
                                <?php 
                                $start = new DateTime($status['data_start']);
                                $now = new DateTime();
                                $duration = $start->diff($now);
                                ?>
                                <div class="detail-item">
                                    <span class="detail-label">Durată Curentă:</span>
                                    <span class="detail-value">
                                        <?php if ($duration->days > 0): ?>
                                            <?= $duration->days ?> zile
                                        <?php endif; ?>
                                        <?php if ($duration->h > 0): ?>
                                            <?= $duration->h ?> ore
                                        <?php endif; ?>
                                        <?php if ($duration->i > 0): ?>
                                            <?= $duration->i ?> minute
                                        <?php endif; ?>
                                        <small style="color: #007bff;"> (în progres)</small>
                                    </span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (isset($status['projects_count'])): ?>
                                <div class="detail-item">
                                    <span class="detail-label">Proiecte cu acest status:</span>
                                    <span class="detail-value projects-using">
                                        <?= $status['projects_count'] ?> proiect(e)
                                    </span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($status['nota_aditionala'])): ?>
                                <div class="detail-item nota-full">
                                    <span class="detail-label">Notă Adițională:</span>
                                    <span class="detail-value"><?= nl2br(htmlspecialchars($status['nota_aditionala'])) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
        <?php else: ?>
            <div class="no-statuses">
                <h3>Nu există statusuri</h3>
                <p>Nu au fost găsite statusuri în baza de date.</p>
                <a href="/statuses/create" style="color: #007bff;">Creează primul status</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function toggleDetails(statusId) {
            const details = document.getElementById('details-' + statusId);
            const icon = document.getElementById('icon-' + statusId);
            
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