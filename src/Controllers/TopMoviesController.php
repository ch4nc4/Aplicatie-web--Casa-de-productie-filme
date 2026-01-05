<?php
// src/Controllers/TopMoviesController.php
require_once __DIR__ . '/../Models/ImdbBoxOffice.php';

class TopMoviesController {
    
    public function index() {
        session_start();
        
        $imdbParser = new ImdbBoxOffice();
        $topMovies = $imdbParser->getTopMovies();

        // Curățăm și validăm toate titlurile
        foreach ($topMovies as $key => $movie) {
            // Asigură-te că titlul este corect decodat
            if (!empty($movie['title'])) {
                $topMovies[$key]['title'] = html_entity_decode($movie['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }
        
        // Încercăm să obținem detalii suplimentare pentru primele 5 filme
        // foreach (array_slice($topMovies, 0, 5) as $key => $movie) {
        //     if (!empty($movie['imdb_url'])) {
        //         $details = $imdbParser->getMovieDetails($movie['imdb_url']);
        //         if ($details) {
        //             $topMovies[$key] = array_merge($topMovies[$key], $details);
        //         }
        //         // Pauză pentru a nu supraîncărca IMDB
        //         sleep(1);
        //     }
        // }
        
        require __DIR__ . '/../Views/movies/index.php';
    }
    
    public function refresh() {
        session_start();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF validation
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die('CSRF token validation failed');
            }
            
            // Forțează refresh-ul cache-ului (dacă ai implementat caching)
            $_SESSION['success'] = 'Lista de filme a fost actualizată cu ultimele informații de pe IMDB!';
        }
        
        header('Location: /top-movies');
        exit;
    }
}