<?php
// Verifică autentificarea și rolul admin
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

// Verifică că utilizatorul este admin
require_once __DIR__ . '/../../Models/User.php';
$userModel = new User();
$currentUser = $userModel->findById($_SESSION['user_id']);

// Obține statistici pentru afișare (dacă nu sunt deja setate din controller)
if (!isset($stats)) {
    require_once __DIR__ . '/../../Models/Project.php';
    $projectModel = new Project();
    $userModel = new User();
    
    $stats = [
        'total_proiecte' => $projectModel->getTotalCount(),
        'total_utilizatori' => $userModel->getTotalCount(),
        'proiecte_active' => $projectModel->getActiveCount(),
        'utilizatori_activi' => $userModel->getActiveCount()
    ];
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Date - Casa de Producție Filme</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .export-header {
            background: linear-gradient(135deg, rgb(102, 15, 34), rgb(36, 36, 101));
            color: white;
            padding: 40px 0;
            border-radius: 0 0 20px 20px;
            margin-bottom: 30px;
        }
        
        .export-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .export-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .export-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        
        .export-card.projects .export-icon { color: #28a745; }
        .export-card.users .export-icon { color: #007bff; }
        .export-card.roles .export-icon { color: #ffc107; }
        .export-card.members .export-icon { color: #17a2b8; }
        
        .btn-export {
            border-radius: 20px;
            padding: 8px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-export:hover {
            transform: scale(1.05);
        }
        
        .stats-overview {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .stat-item {
            text-align: center;
            padding: 15px;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .export-description {
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 10px;
        }
        
        .download-format {
            display: inline-block;
            padding: 4px 12px;
            background: #e9ecef;
            border-radius: 15px;
            font-size: 0.8rem;
            margin: 2px;
            color: #495057;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="export-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="fas fa-download me-3"></i>Export Date</h1>
                    <p class="mb-0 lead">Exportă informațiile din aplicație în formate Excel (CSV) pentru analiză externă</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="/admin/users" class="btn btn-outline-light me-2">
                        <i class="fas fa-users me-1"></i>Utilizatori
                    </a>
                    <a href="/statistics" class="btn btn-outline-light">
                        <i class="fas fa-chart-bar me-1"></i>Statistici
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Afișare mesaje -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?= htmlspecialchars($_SESSION['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= htmlspecialchars($_SESSION['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <!-- Statistici Overview -->
        <div class="stats-overview">
            <h4 class="mb-4"><i class="fas fa-info-circle me-2"></i>Rezumat Date Disponibile</h4>
            <div class="row">
                <div class="col-md-3 stat-item">
                    <div class="stat-number text-success"><?= number_format($stats['total_proiecte']) ?></div>
                    <div class="stat-label">Total Proiecte</div>
                </div>
                <div class="col-md-3 stat-item">
                    <div class="stat-number text-primary"><?= number_format($stats['total_utilizatori']) ?></div>
                    <div class="stat-label">Total Utilizatori</div>
                </div>
                <div class="col-md-3 stat-item">
                    <div class="stat-number text-warning"><?= number_format($stats['proiecte_active']) ?></div>
                    <div class="stat-label">Proiecte Active</div>
                </div>
                <div class="col-md-3 stat-item">
                    <div class="stat-number text-info"><?= number_format($stats['utilizatori_activi']) ?></div>
                    <div class="stat-label">Utilizatori Activi</div>
                </div>
            </div>
        </div>

        <!-- Opțiuni Export -->
        <h2 class="mb-4"><i class="fas fa-file-export me-2"></i>Opțiuni de Export</h2>
        
        <div class="row">
            <!-- Export Proiecte Complete -->
            <div class="col-md-6 mb-4">
                <div class="card export-card projects h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-project-diagram export-icon"></i>
                        <h5 class="card-title">Raport Proiecte Complete</h5>
                        <p class="card-text">
                            Lista completă cu toate proiectele, incluzând detalii despre status, buget, durată și membri echipă.
                        </p>
                        <div class="export-description">
                            <strong>Include:</strong> ID, Titlu, Tip, Status, Buget, Perioada, Descriere, Nr. membri, Date creare/actualizare
                        </div>
                        <div class="mt-3">
                            <span class="download-format">Excel (CSV)</span>
                            <span class="download-format">UTF-8</span>
                        </div>
                        <div class="mt-4">
                            <button onclick="exportData('/export/projects/excel')" class="btn btn-success btn-export">
                                <i class="fas fa-file-excel me-1"></i>Descarcă Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Proiecte pe Categorii -->
            <div class="col-md-6 mb-4">
                <div class="card export-card projects h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-pie export-icon"></i>
                        <h5 class="card-title">Proiecte pe Categorii</h5>
                        <p class="card-text">
                            Analiză sumară a proiectelor grupate pe tipuri/categorii cu statistici financiare.
                        </p>
                        <div class="export-description">
                            <strong>Include:</strong> Tip/Categorie, Nr. proiecte, Buget total, Buget mediu, Status predominant
                        </div>
                        <div class="mt-3">
                            <span class="download-format">Excel (CSV)</span>
                            <span class="download-format">Analiză agregată</span>
                        </div>
                        <div class="mt-4">
                            <button onclick="exportData('/export/projects/categories')" class="btn btn-success btn-export">
                                <i class="fas fa-file-excel me-1"></i>Descarcă Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Utilizatori -->
            <div class="col-md-6 mb-4">
                <div class="card export-card users h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-users export-icon"></i>
                        <h5 class="card-title">Utilizatori și Roluri</h5>
                        <p class="card-text">
                            Lista completă a utilizatorilor cu informații despre roluri, activitate și participare la proiecte.
                        </p>
                        <div class="export-description">
                            <strong>Include:</strong> Date personale, Email, Roluri, Nr. proiecte active, Activitate recentă, Status cont
                        </div>
                        <div class="mt-3">
                            <span class="download-format">Excel (CSV)</span>
                            <span class="download-format">Date GDPR compliant</span>
                        </div>
                        <div class="mt-4">
                            <button onclick="exportData('/export/users/excel')" class="btn btn-primary btn-export">
                                <i class="fas fa-file-excel me-1"></i>Descarcă Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Distribuție Roluri -->
            <div class="col-md-6 mb-4">
                <div class="card export-card roles h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-user-tag export-icon"></i>
                        <h5 class="card-title">Distribuția Rolurilor</h5>
                        <p class="card-text">
                            Analiză statistică a distribuției rolurilor în organizație cu procente și comparații.
                        </p>
                        <div class="export-description">
                            <strong>Include:</strong> Nume rol, Nr. utilizatori, Procent din total, Utilizatori activi/inactivi
                        </div>
                        <div class="mt-3">
                            <span class="download-format">Excel (CSV)</span>
                            <span class="download-format">Analiză statistică</span>
                        </div>
                        <div class="mt-4">
                            <button onclick="exportData('/export/roles/distribution')" class="btn btn-warning btn-export">
                                <i class="fas fa-file-excel me-1"></i>Descarcă Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Membri Proiecte -->
            <div class="col-md-6 mb-4">
                <div class="card export-card members h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-users-cog export-icon"></i>
                        <h5 class="card-title">Membri pe Proiecte</h5>
                        <p class="card-text">
                            Raport detaliat cu toți membrii echipelor pe proiecte și rolurile lor specifice.
                        </p>
                        <div class="export-description">
                            <strong>Include:</strong> Proiect, Tip proiect, Membru, Email, Rol în proiect, Data alăturare, Observații
                        </div>
                        <div class="mt-3">
                            <span class="download-format">Excel (CSV)</span>
                            <span class="download-format">Relații proiecte-membri</span>
                        </div>
                        <div class="mt-4">
                            <button onclick="exportData('/export/project-members/excel')" class="btn btn-info btn-export">
                                <i class="fas fa-file-excel me-1"></i>Descarcă Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informații suplimentare -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-info">
                    <div class="card-body">
                        <h5 class="card-title text-info">
                            <i class="fas fa-info-circle me-2"></i>Informații despre Export
                        </h5>
                        <div class="row">
                            <div class="col-md-4">
                                <h6><i class="fas fa-file-excel me-2"></i>Format Fișiere</h6>
                                <ul class="list-unstyled">
                                    <li>• CSV cu encoding UTF-8</li>
                                    <li>• Compatibil Excel/LibreOffice</li>
                                    <li>• Separatori standard</li>
                                    <li>• Headers descriptive</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h6><i class="fas fa-shield-alt me-2"></i>Securitate</h6>
                                <ul class="list-unstyled">
                                    <li>• Acces doar pentru Admin</li>
                                    <li>• Log-uri pentru audit</li>
                                    <li>• Date anonimizate opțional</li>
                                    <li>• Respectare GDPR</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h6><i class="fas fa-clock me-2"></i>Frecvență</h6>
                                <ul class="list-unstyled">
                                    <li>• Export în timp real</li>
                                    <li>• Date actualizate automat</li>
                                    <li>• Backup recomandat săptămânal</li>
                                    <li>• Arhivare lunară</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h6>Se generează fișierul...</h6>
                    <p class="mb-0 text-muted">Vă rugăm așteptați</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function exportData(url) {
            // Afișează loading modal
            const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
            loadingModal.show();
            
            // Creează un element pentru download
            const link = document.createElement('a');
            link.href = url;
            link.style.display = 'none';
            document.body.appendChild(link);
            
            // Simulează delay pentru loading (opțional)
            setTimeout(() => {
                link.click();
                document.body.removeChild(link);
                
                // Ascunde loading modal după 2 secunde
                setTimeout(() => {
                    loadingModal.hide();
                }, 2000);
            }, 500);
        }
        
        function exportAllData() {
            if (confirm('Acest export va genera toate tipurile de rapoarte. Continuați?')) {
                exportData('/export/all/archive');
            }
        }
        
        // Auto-refresh pentru statistici la fiecare 30 secunde
        setInterval(function() {
            // Poți implementa refresh automat al statisticilor dacă dorești
            fetch('/api/stats/summary')
                .then(response => response.json())
                .then(data => {
                    // Actualizează statisticile în timp real
                    updateStats(data);
                })
                .catch(error => console.log('Stats update failed:', error));
        }, 30000);
        
        function updateStats(data) {
            // Implementează actualizarea statisticilor în timp real
            if (data.total_proiecte) {
                document.querySelector('.stat-number.text-success').textContent = new Intl.NumberFormat().format(data.total_proiecte);
            }
            // ... alte actualizări
        }
    </script>
</body>
</html>
