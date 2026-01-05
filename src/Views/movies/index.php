<?php
// src/Views/movies/top.php
session_start();
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cele mai bune filme în momentul acesta - Casa de Producție Filme</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
            padding: 20px; 
            background: white;
            min-height: 100vh;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px 0;
            border-bottom: 2px solid #007bff;
        }
        .header h1 {
            color: #333;
            margin: 0;
            font-size: 2.5em;
        }
        .header p {
            color: #666;
            font-size: 1.1em;
            margin: 10px 0;
        }
        .movie-card {
            display: flex;
            border: 1px solid #ddd;
            margin-bottom: 20px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            background: white;
        }
        .movie-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }
        .movie-poster {
            width: 120px;
            height: 180px;
            object-fit: cover;
            background-color: #f0f0f0;
        }
        .movie-content {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .movie-rank {
            font-size: 2em;
            font-weight: bold;
            color: #007bff;
            margin-right: 15px;
            min-width: 50px;
        }
        .movie-title {
            font-size: 1.4em;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }
        .movie-year {
            color: #666;
            font-size: 1.1em;
            margin-bottom: 10px;
        }
        .movie-gross {
            background: #28a745;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 10px;
        }
        .movie-rating {
            background: #ffc107;
            color: #212529;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: bold;
            display: inline-block;
            margin: 0 10px 10px 0;
        }
        .movie-description {
            color: #555;
            line-height: 1.6;
            margin-top: 10px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #007bff;
            text-decoration: none;
            font-size: 1.1em;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .refresh-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            margin: 10px 0;
        }
        .refresh-btn:hover {
            background: #0056b3;
        }
        .success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .no-poster {
            width: 120px;
            height: 180px;
            background: linear-gradient(45deg, #f0f0f0, #e0e0e0);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 12px;
            text-align: center;
        }
        .movie-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }
        .source-credit {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/" class="back-link">← Înapoi la pagina principală</a>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="success"><?= htmlspecialchars($_SESSION['success']) ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <div class="header">
            <h1>🎬 Cele mai bune filme în momentul acesta</h1>
            <p>Top filme la box office conform IMDB</p>
            
            <form method="POST" action="/top-movies/refresh" style="display: inline;">
                <?= csrf_token_field() ?>
                <button type="submit" class="refresh-btn">🔄 Actualizează lista</button>
            </form>
        </div>
        
        <?php if (!empty($topMovies)): ?>
            <?php foreach ($topMovies as $movie): ?>
               <div class="movie-card">
                    <div class="movie-rank">#<?= htmlspecialchars($movie['position']) ?></div>
                    
                    <!-- Nu mai afișăm poster, folosim placeholder -->
                    <div class="no-poster">
                        🎬<br>
                        <?= htmlspecialchars($movie['title']) ?>
                    </div>
                    
                    <div class="movie-content">
                        <div class="movie-title"><?= htmlspecialchars($movie['title']) ?></div>
                        
                        <?php if (!empty($movie['year'])): ?>
                            <div class="movie-year">📅 <?= htmlspecialchars($movie['year']) ?></div>
                        <?php endif; ?>
                        
                        <div class="movie-meta">
                            <?php if (!empty($movie['rating'])): ?>
                                <span class="movie-rating">
                                    ⭐ <?= htmlspecialchars($movie['rating']) ?>/10
                                    <?php if (!empty($movie['rating_count'])): ?>
                                        (<?= htmlspecialchars($movie['rating_count']) ?>)
                                    <?php endif; ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if (!empty($movie['weekend_gross'])): ?>
                                <span class="movie-gross">
                                    🎬 Weekend: <?= htmlspecialchars($movie['weekend_gross']) ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if (!empty($movie['total_gross'])): ?>
                                <span class="movie-gross" style="background: #17a2b8;">
                                    💰 Total: <?= htmlspecialchars($movie['total_gross']) ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if (!empty($movie['weeks_released'])): ?>
                                <span class="movie-rating" style="background: #6c757d;">
                                    📅 <?= htmlspecialchars($movie['weeks_released']) ?> săptămâni
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Nu mai afișăm descriere -->
                        
                        <?php if (!empty($movie['imdb_url'])): ?>
                            <div style="margin-top: 10px;">
                                <a href="<?= htmlspecialchars($movie['imdb_url']) ?>" 
                                target="_blank" 
                                style="color: #007bff; text-decoration: none;">
                                    🔗 Vezi pe IMDB
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; color: #666;">
                <h3>Nu s-au putut încărca filmele în acest moment</h3>
                <p>Te rugăm să încerci din nou mai târziu</p>
                
                <form method="POST" action="/top-movies/refresh">
                    <?= csrf_token_field() ?>
                    <button type="submit" class="refresh-btn">🔄 Încearcă din nou</button>
                </form>
            </div>
        <?php endif; ?>
        
        <div class="source-credit">
            <p>Informații preluate de pe IMDB Box Office • Actualizat la <?= date('d.m.Y H:i') ?></p>
        </div>
    </div>
</body>
</html>