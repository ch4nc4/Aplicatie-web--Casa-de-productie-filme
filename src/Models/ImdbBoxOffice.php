<?php
// src/Models/ImdbBoxOffice.php
class ImdbBoxOffice {
    
    public function getTopMovies() {
        $url = "https://www.imdb.com/chart/boxoffice/?ref_=hm_nv_menu";
        
        $ch = curl_init($url);
        
        // Opțiuni cURL
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) '
                               . 'AppleWebKit/537.36 (KHTML, like Gecko) '
                               . 'Chrome/118.0.5993.90 Safari/537.36',
            CURLOPT_HTTPHEADER => [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language: en-EN,en;q=0.9,en-US;q=0.8,en;q=0.7',
                'Connection: keep-alive',
                'Upgrade-Insecure-Requests: 1',
                'Cache-Control: max-age=0',
            ],
        ]);
        
        $continut = curl_exec($ch);
        $movies = [];
        
        if (curl_errno($ch)) {
            error_log('Eroare cURL IMDB: ' . curl_error($ch));
            return [];
        }

            
        // Debug 
        file_put_contents('/tmp/debug_imdb.html', $continut);
        
        $movies = $this->parseNewStructure($continut);
        
        curl_close($ch);
        return $movies;
    }

    private function parseNewStructure($continut) {
        $movies = [];
        
        // Pattern corect care capturează de la un ipc-metadata-list-summary-item până la următorul sau sfârșitul
         if (preg_match_all('/<li[^>]*class="[^"]*ipc-metadata-list-summary-item[^"]*"[^>]*>(.*?)(?=<li[^>]*class="[^"]*ipc-metadata-list-summary-item)/s', $continut, $movieMatches)) {
            
            foreach ($movieMatches[1] as $index => $movieHtml) {
                if ($index >= 10) break; // Primele 10
                
                $movie = [
                    'position' => $index + 1,
                    'title' => '',
                    'year' => '',
                    'weekend_gross' => '',
                    'total_gross' => '',
                    'weeks_released' => '',
                    'rating' => '',
                    'rating_count' => '',
                    'imdb_url' => '',
                    'poster_url' => ''
                ];
                
                // DEBUG pentru primul film
                if ($index == 0) {
                    file_put_contents('/tmp/first_movie_full.html', $movieHtml);
                    error_log("HTML complet primul film salvat - lungime: " . strlen($movieHtml));
                }
                
                // Extragem titlul din h3 cu clasa ipc-title__text
                if (preg_match('/<h3[^>]*class="[^"]*ipc-title__text[^"]*"[^>]*>([^<]+)<\/h3>/', $movieHtml, $titleMatches)) {
                    $movie['title'] = html_entity_decode(trim($titleMatches[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                }
                
                // Extragem URL-ul IMDB din link-ul către titlu
                if (preg_match('/<a[^>]*href="([^"]*)"[^>]*class="[^"]*ipc-title-link-wrapper[^"]*"/', $movieHtml, $urlMatches)) {
                    $movie['imdb_url'] = 'https://www.imdb.com' . $urlMatches[1];
                }
                
                // Extragem posterul din img cu clasa ipc-image
                if (preg_match('/<img[^>]*src="([^"]+)"[^>]*class="[^"]*ipc-image[^"]*"/', $movieHtml, $posterMatches)) {
                    $movie['poster_url'] = $posterMatches[1];
                }
                
                // Extragem anul din metadata
                if (preg_match('/<span[^>]*class="[^"]*cli-title-metadata-item[^"]*"[^>]*>(\d{4})<\/span>/', $movieHtml, $yearMatches)) {
                    $movie['year'] = $yearMatches[1];
                }
                
                // PARSARE BOX OFFICE din structura exactă
                if (preg_match('/<ul[^>]*class="[^"]*sc-382281d-0[^"]*dyEwhs[^"]*"[^>]*data-testid="title-metadata-box-office-data-container"[^>]*>(.*?)<\/ul>/s', $movieHtml, $boxOfficeMatch)) {
                    
                    $boxOfficeHtml = $boxOfficeMatch[1];
                    
                    // Weekend Gross cu pattern exact pentru li element
                    if (preg_match('/<li[^>]*class="[^"]*sc-382281d-1[^"]*"[^>]*><span>Weekend Gross[^:]*:\s*<\/span><span[^>]*class="[^"]*sc-382281d-2[^"]*"[^>]*>\$([^<]+)<\/span><\/li>/s', $boxOfficeHtml, $weekendMatch)) {
                        $movie['weekend_gross'] = '$' . trim($weekendMatch[1]);
                    }
                    
                    // Total Gross cu pattern exact pentru li element
                    if (preg_match('/<li[^>]*class="[^"]*sc-382281d-1[^"]*"[^>]*><span>Total Gross[^:]*:\s*<\/span><span[^>]*class="[^"]*sc-382281d-2[^"]*"[^>]*>\$([^<]+)<\/span><\/li>/s', $boxOfficeHtml, $totalMatch)) {
                        $movie['total_gross'] = '$' . trim($totalMatch[1]);
                    }
                    
                    // Weeks Released cu pattern exact pentru li element
                    if (preg_match('/<li[^>]*class="[^"]*sc-382281d-1[^"]*"[^>]*><span>Weeks Released[^:]*:\s*<\/span><span[^>]*class="[^"]*sc-382281d-2[^"]*"[^>]*>([^<]+)<\/span><\/li>/s', $boxOfficeHtml, $weeksMatch)) {
                        $movie['weeks_released'] = trim($weeksMatch[1]);
                    }
                    
                    if ($index == 0) {
                        file_put_contents('/tmp/box_office_section.html', $boxOfficeHtml);
                        error_log("Box office section salvată");
                    }
                }
                
                // PARSARE RATING din structura exactă
                if (preg_match('/aria-label="IMDb rating: ([0-9.]+)"/', $movieHtml, $ratingMatch)) {
                    $movie['rating'] = trim($ratingMatch[1]);
                }
                
                // PARSARE RATING COUNT din structura exactă
                if (preg_match('/<span[^>]*class="[^"]*ipc-rating-star--voteCount[^"]*"[^>]*>&nbsp;\(<!-- -->([^<]+)<!-- -->\)<\/span>/', $movieHtml, $voteMatch)) {
                    $movie['rating_count'] = trim($voteMatch[1]);
                }
                
                // DEBUG pentru primul film
                if ($index == 0) {
                    error_log("Titlu găsit: " . $movie['title']);
                    error_log("IMDB URL: " . $movie['imdb_url']);
                    error_log("Poster: " . $movie['poster_url']);
                    error_log("Rating găsit: " . $movie['rating']);
                    error_log("Rating count găsit: " . $movie['rating_count']);
                    error_log("Weekend gross găsit: " . $movie['weekend_gross']);
                    error_log("Total gross găsit: " . $movie['total_gross']);
                    error_log("Weeks released găsit: " . $movie['weeks_released']);
                }
                
                // Adaugă doar dacă are titlu
                if (!empty($movie['title'])) {
                    $movies[] = $movie;
                }
            }
        } else {
            error_log("NU s-au găsit filme cu pattern-ul ipc-metadata-list-summary-item");
        }
        
        return $movies;
    }

    
    public function getMovieDetails($imdbUrl) {
        if (empty($imdbUrl)) return null;
        
        $ch = curl_init($imdbUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.5993.90 Safari/537.36',
        ]);
        
        $html = curl_exec($ch);
        $details = [];
        
        if (!curl_errno($ch) && $html) {
            // Extragem posterul
            if (preg_match('/<img[^>]*class="[^"]*poster[^"]*"[^>]*src="([^"]+)"/', $html, $matches)) {
                $details['poster_url'] = $matches[1];
            }
            
            // Extragem rating-ul
            if (preg_match('/(\d+\.\d+)<\/span><span>\/10/', $html, $matches)) {
                $details['rating'] = $matches[1];
            }
            
            // Extragem descrierea
            if (preg_match('/<span[^>]*data-testid="plot-xl"[^>]*>([^<]+)<\/span>/', $html, $matches)) {
                $details['description'] = trim($matches[1]);
            }
        }
        
        curl_close($ch);
        return $details;
    }
}