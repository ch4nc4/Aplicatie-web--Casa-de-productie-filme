<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vizualizează Watchlist - Casa de Producție</title>
    <style>
        .container { max-width: 1000px; margin: 0 auto; padding: 20px; }
        .back-link { color: #007bff; text-decoration: none; margin-bottom: 20px; display: inline-block; }
        .back-link:hover { text-decoration: underline; }
        .watchlist-item { border: 1px solid #ddd; border-radius: 8px; margin-bottom: 15px; background: white; }
        .watchlist-header { padding: 15px 20px; cursor: pointer; background: #f8f9fa; border-radius: 8px 8px 0 0; display: flex; justify-content: space-between; align-items: center; }
        .watchlist-header:hover { background: #e9ecef; }
        .watchlist-title { font-size: 1.3em; font-weight: bold; color: #333; }
        .watchlist-meta { color: #666; font-size: 0.9em; }
        .dropdown-icon { font-size: 1.2em; transition: transform 0.3s ease; color: #666; }
        .dropdown-icon.rotated { transform: rotate(180deg); }
        .watchlist-details { padding: 0 20px 20px 20px; display: none; background: #fff; border-radius: 0 0 8px 8px; }
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 15px; }
        .detail-item { padding: 10px; background: #f8f9fa; border-radius: 4px; }
        .detail-label { font-weight: bold; color: #333; display: block; margin-bottom: 5px; }
        .detail-value { color: #666; }
        .description-full { grid-column: 1 / -1; }
        .no-watchlistItem { text-align: center; padding: 40px; color: #666; }
        .watchlistItem-count { color: #666; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="/views/watchlist" class="back-link">← Înapoi la meniul watchlist</a>
        
        <h2>Toate elementele din Watchlist</h2>
        
        <?php if (!empty($watchlistItems)): ?>
            <div class="watchlistItem-count">
                <strong>Total: <?= count($watchlistItems) ?> elemente găsite</strong>
            </div>
            
            <?php foreach ($watchlistItems as $item): ?>
            <div class="watchlist-item">
                <div class="watchlist-header" onclick="toggleDetails(<?= $item['id'] ?>)">
                    <div>
                        <div class="watchlist-title"><?= htmlspecialchars($item['title']) ?></div>
                        <div class="watchlist-meta">
                            Proiect ID: <?= $item['id_proiect'] ?> | 
                            Adăugat la: <?= htmlspecialchars($item['added_at']) ?>
                        </div>
                    </div>
                    <div class="dropdown-icon" id="icon-<?= $item['id'] ?>">▼</div>
                    <!-- Delete button -->
                    <form method="POST" action="/watchlist/delete" style="display:inline; margin-left: 15px;" onsubmit="return confirm('Sigur vrei să ștergi acest element din watchlist?');">
                        <?php echo csrf_token_field(); ?>
                        <input type="hidden" name="user_id" value="<?= htmlspecialchars($item['id_user']) ?>">
                        <input type="hidden" name="project_id" value="<?= htmlspecialchars($item['id_proiect']) ?>">
                        <button type="submit" class="btn btn-danger" style="background:#dc3545;color:#fff;border:none;padding:6px 12px;border-radius:4px;cursor:pointer;">Șterge</button>
                    </form>
                </div>
                
                <div class="watchlist-details" id="details-<?= $item['id'] ?>">
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">ID Proiect:</span>
                            <span class="detail-value"><?= htmlspecialchars($item['id_proiect']) ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Titlu:</span>
                            <span class="detail-value"><?= htmlspecialchars($item['title']) ?></span>
                        </div>
                        <?php if (!empty($item['descriere'])): ?>
                            <div class="detail-item description-full">
                                <span class="detail-label">Descriere:</span>
                                <span class="detail-value"><?= nl2br(htmlspecialchars($item['descriere'])) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($item['poster_url'])): ?>
                            <div class="detail-item">
                                <span class="detail-label">Poster:</span>
                                <span class="detail-value">
                                    <a href="<?= htmlspecialchars($item['poster_url']) ?>" target="_blank" style="color: #007bff;">Vezi poster</a>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
            
        <?php else: ?>
            <div class="no-watchlistItem">
                <h3>Nu există elemente în watchlist</h3>
                <p>Nu au fost găsite elemente în watchlist pentru acest utilizator.</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function toggleDetails(itemId) {
            const details = document.getElementById('details-' + itemId);
            const icon = document.getElementById('icon-' + itemId);
            
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