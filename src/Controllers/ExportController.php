<?php

namespace App\Controllers;

require_once __DIR__ . '/../Models/Project.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/ProjectMember.php';
require_once __DIR__ . '/../../vendor/autoload.php';

class ExportController {
    private $projectModel;
    private $userModel;
    private $projectMemberModel;

    public function __construct() {
        $this->projectModel = new \Project();
        $this->userModel = new \User();
        $this->projectMemberModel = new \ProjectMember();
    }

    /**
     * Exportă lista completă de proiecte în PDF
     */
    public function exportProjectsPDF() {
        if (!$this->checkAdminAccess()) return;

        try {
            $projects = $this->projectModel->getAllWithDetails();
            
            // Creează un nou document PDF
            $pdf = new \TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
            
            // Setează informațiile documentului
            $pdf->SetCreator('Casa de Producție Filme');
            $pdf->SetAuthor('Administrator');
            $pdf->SetTitle('Raport Complet Proiecte');
            $pdf->SetSubject('Export proiecte');
            
            // Setează marginile
            $pdf->SetMargins(10, 20, 10);
            $pdf->SetHeaderMargin(10);
            $pdf->SetFooterMargin(15);
            
            // Setează auto page break
            $pdf->SetAutoPageBreak(true, 25);
            
            // Setează font-ul
            $pdf->SetFont('DejaVuSans', '', 10);
            
            // Adaugă pagina
            $pdf->AddPage();
            
            // Titlul
            $pdf->SetFont('DejaVuSans', 'B', 16);
            $pdf->Cell(0, 15, 'RAPORT COMPLET PROIECTE', 0, 1, 'C');
            $pdf->Cell(0, 10, 'Data generării: ' . date('d.m.Y H:i'), 0, 1, 'C');
            $pdf->Ln(10);
            
            // Header tabel
            $pdf->SetFont('DejaVuSans', 'B', 8);
            $pdf->SetFillColor(240, 240, 240);
            
            $headers = [
                ['text' => 'ID', 'width' => 15],
                ['text' => 'Titlu Proiect', 'width' => 45],
                ['text' => 'Tip', 'width' => 25],
                ['text' => 'Status', 'width' => 25],
                ['text' => 'Buget (RON)', 'width' => 30],
                ['text' => 'Data Început', 'width' => 25],
                ['text' => 'Data Sfârșit', 'width' => 25],
                ['text' => 'Durată (zile)', 'width' => 25],
                ['text' => 'Nr. Membri', 'width' => 20],
                ['text' => 'Data Creare', 'width' => 25]
            ];
            
            foreach ($headers as $header) {
                $pdf->Cell($header['width'], 8, $header['text'], 1, 0, 'C', true);
            }
            $pdf->Ln();
            
            // Datele
            $pdf->SetFont('DejaVuSans', '', 7);
            $pdf->SetFillColor(255, 255, 255);
            
            foreach ($projects as $index => $project) {
                $startDate = $project['data_inceput'] ? new \DateTime($project['data_inceput']) : null;
                $endDate = $project['data_sfarsit'] ? new \DateTime($project['data_sfarsit']) : null;
                $duration = ($startDate && $endDate) ? $startDate->diff($endDate)->days : 'N/A';
                
                // Alternează culoarea rândurilor
                $fill = ($index % 2 == 0) ? false : true;
                if ($fill) {
                    $pdf->SetFillColor(248, 248, 248);
                } else {
                    $pdf->SetFillColor(255, 255, 255);
                }
                
                $pdf->Cell(15, 6, $project['id'], 1, 0, 'C', $fill);
                $pdf->Cell(45, 6, $this->truncateText($project['title'], 25), 1, 0, 'L', $fill);
                $pdf->Cell(25, 6, $this->truncateText($project['tip'] ?? 'Nespecificat', 15), 1, 0, 'L', $fill);
                $pdf->Cell(25, 6, $this->truncateText($project['status'], 15), 1, 0, 'L', $fill);
                $pdf->Cell(30, 6, number_format($project['buget'] ?? 0, 0), 1, 0, 'R', $fill);
                $pdf->Cell(25, 6, $project['data_inceput'] ? date('d.m.Y', strtotime($project['data_inceput'])) : 'N/A', 1, 0, 'C', $fill);
                $pdf->Cell(25, 6, $project['data_sfarsit'] ? date('d.m.Y', strtotime($project['data_sfarsit'])) : 'N/A', 1, 0, 'C', $fill);
                $pdf->Cell(25, 6, $duration, 1, 0, 'C', $fill);
                $pdf->Cell(20, 6, $project['nr_membri'] ?? 0, 1, 0, 'C', $fill);
                $pdf->Cell(25, 6, $project['created_at'] ? date('d.m.Y', strtotime($project['created_at'])) : 'N/A', 1, 0, 'C', $fill);
                $pdf->Ln();
            }
            
            // Statistici finale
            $pdf->Ln(10);
            $pdf->SetFont('DejaVuSans', 'B', 10);
            $pdf->Cell(0, 8, 'STATISTICI:', 0, 1, 'L');
            $pdf->SetFont('DejaVuSans', '', 10);
            $pdf->Cell(0, 6, 'Total proiecte: ' . count($projects), 0, 1, 'L');
            
            $totalBuget = array_sum(array_column($projects, 'buget'));
            $pdf->Cell(0, 6, 'Buget total: ' . number_format($totalBuget, 2) . ' RON', 0, 1, 'L');
            
            $filename = 'proiecte_complete_' . date('Y-m-d_H-i-s') . '.pdf';
            
            // Output PDF
            $pdf->Output($filename, 'D');
            
        } catch (Exception $e) {
            error_log("Eroare export proiecte PDF: " . $e->getMessage());
            $_SESSION['error'] = 'Eroare la exportul proiectelor: ' . $e->getMessage();
            header('Location: /admin/exports');
        }
    }

    /**
     * Exportă proiecte pe categorii în PDF
     */
    public function exportProjectsByCategoryPDF() {
        if (!$this->checkAdminAccess()) return;

        try {
            $projectsByCategory = $this->projectModel->getProjectsByCategory();
            
            $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            
            $pdf->SetCreator('Casa de Producție Filme');
            $pdf->SetAuthor('Administrator');
            $pdf->SetTitle('Raport Proiecte pe Categorii');
            
            $pdf->SetMargins(15, 20, 15);
            $pdf->SetAutoPageBreak(true, 25);
            
            $pdf->SetFont('DejaVuSans', '', 12);
            $pdf->AddPage();
            
            // Titlul
            $pdf->SetFont('DejaVuSans', 'B', 16);
            $pdf->Cell(0, 15, 'RAPORT PROIECTE PE CATEGORII', 0, 1, 'C');
            $pdf->Cell(0, 10, 'Data generării: ' . date('d.m.Y H:i'), 0, 1, 'C');
            $pdf->Ln(10);
            
            // Header tabel
            $pdf->SetFont('DejaVuSans', 'B', 10);
            $pdf->SetFillColor(240, 240, 240);
            
            $pdf->Cell(50, 10, 'Categorie/Tip', 1, 0, 'C', true);
            $pdf->Cell(30, 10, 'Nr. Proiecte', 1, 0, 'C', true);
            $pdf->Cell(40, 10, 'Buget Total (RON)', 1, 0, 'C', true);
            $pdf->Cell(40, 10, 'Buget Mediu (RON)', 1, 0, 'C', true);
            $pdf->Cell(30, 10, 'Status Predominant', 1, 1, 'C', true);
            
            // Datele
            $pdf->SetFont('DejaVuSans', '', 9);
            
            foreach ($projectsByCategory as $index => $category) {
                $fill = ($index % 2 == 0) ? false : true;
                if ($fill) {
                    $pdf->SetFillColor(248, 248, 248);
                } else {
                    $pdf->SetFillColor(255, 255, 255);
                }
                
                $pdf->Cell(50, 8, $category['tip'] ?: 'Necategorizat', 1, 0, 'L', $fill);
                $pdf->Cell(30, 8, $category['numar_proiecte'], 1, 0, 'C', $fill);
                $pdf->Cell(40, 8, number_format($category['buget_total'] ?? 0, 0), 1, 0, 'R', $fill);
                $pdf->Cell(40, 8, number_format($category['buget_mediu'] ?? 0, 0), 1, 0, 'R', $fill);
                $pdf->Cell(30, 8, $this->truncateText($category['status_predominant'] ?? 'N/A', 12), 1, 1, 'C', $fill);
            }
            
            $filename = 'proiecte_categorii_' . date('Y-m-d_H-i-s') . '.pdf';
            $pdf->Output($filename, 'D');
            
        } catch (Exception $e) {
            error_log("Eroare export categorii PDF: " . $e->getMessage());
            $_SESSION['error'] = 'Eroare la exportul pe categorii: ' . $e->getMessage();
            header('Location: /admin/exports');
        }
    }

    /**
     * Exportă utilizatori cu roluri în PDF
     */
    public function exportUsersPDF() {
        if (!$this->checkAdminAccess()) return;

        try {
            $users = $this->userModel->getAllWithRoles();
            
            $pdf = new \TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
            
            $pdf->SetCreator('Casa de Producție Filme');
            $pdf->SetAuthor('Administrator');
            $pdf->SetTitle('Raport Utilizatori cu Roluri');
            
            $pdf->SetMargins(10, 20, 10);
            $pdf->SetAutoPageBreak(true, 25);
            
            $pdf->SetFont('DejaVuSans', '', 10);
            $pdf->AddPage();
            
            // Titlul
            $pdf->SetFont('DejaVuSans', 'B', 16);
            $pdf->Cell(0, 15, 'RAPORT UTILIZATORI CU ROLURI', 0, 1, 'C');
            $pdf->Cell(0, 10, 'Data generării: ' . date('d.m.Y H:i'), 0, 1, 'C');
            $pdf->Ln(10);
            
            // Header tabel
            $pdf->SetFont('DejaVuSans', 'B', 9);
            $pdf->SetFillColor(240, 240, 240);
            
            $pdf->Cell(20, 8, 'ID', 1, 0, 'C', true);
            $pdf->Cell(40, 8, 'Nume Utilizator', 1, 0, 'C', true);
            $pdf->Cell(50, 8, 'Email', 1, 0, 'C', true);
            $pdf->Cell(30, 8, 'Nume Complet', 1, 0, 'C', true);
            $pdf->Cell(40, 8, 'Roluri', 1, 0, 'C', true);
            $pdf->Cell(30, 8, 'Nr. Proiecte', 1, 0, 'C', true);
            $pdf->Cell(25, 8, 'Status', 1, 0, 'C', true);
            $pdf->Cell(30, 8, 'Data Înregistrare', 1, 1, 'C', true);
            
            // Datele
            $pdf->SetFont('DejaVuSans', '', 8);
            
            foreach ($users as $index => $user) {
                $fill = ($index % 2 == 0) ? false : true;
                if ($fill) {
                    $pdf->SetFillColor(248, 248, 248);
                } else {
                    $pdf->SetFillColor(255, 255, 255);
                }
                
                $pdf->Cell(20, 6, $user['id'], 1, 0, 'C', $fill);
                $pdf->Cell(40, 6, $this->truncateText($user['username'], 20), 1, 0, 'L', $fill);
                $pdf->Cell(50, 6, $this->truncateText($user['email'], 25), 1, 0, 'L', $fill);
                $pdf->Cell(30, 6, $this->truncateText(($user['nume'] ?? '') . ' ' . ($user['prenume'] ?? ''), 15), 1, 0, 'L', $fill);
                $pdf->Cell(40, 6, $this->truncateText($user['roluri'] ?? 'Fără rol', 20), 1, 0, 'L', $fill);
                $pdf->Cell(30, 6, $user['numar_proiecte'] ?? 0, 1, 0, 'C', $fill);
                $pdf->Cell(25, 6, $user['activ'] ? 'Activ' : 'Inactiv', 1, 0, 'C', $fill);
                $pdf->Cell(30, 6, $user['created_at'] ? date('d.m.Y', strtotime($user['created_at'])) : 'N/A', 1, 1, 'C', $fill);
            }
            
            $filename = 'utilizatori_roluri_' . date('Y-m-d_H-i-s') . '.pdf';
            $pdf->Output($filename, 'D');
            
        } catch (Exception $e) {
            error_log("Eroare export utilizatori PDF: " . $e->getMessage());
            $_SESSION['error'] = 'Eroare la exportul utilizatorilor: ' . $e->getMessage();
            header('Location: /admin/exports');
        }
    }

    /**
     * Exportă distribuția rolurilor în PDF
     */
    public function exportRoleDistributionPDF() {
        if (!$this->checkAdminAccess()) return;

        try {
            $roleDistribution = $this->userModel->getRoleDistribution();
            
            $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            
            $pdf->SetCreator('Casa de Producție Filme');
            $pdf->SetAuthor('Administrator');
            $pdf->SetTitle('Distribuția Rolurilor');
            
            $pdf->SetMargins(15, 20, 15);
            $pdf->SetAutoPageBreak(true, 25);
            
            $pdf->SetFont('DejaVuSans', '', 12);
            $pdf->AddPage();
            
            // Titlul
            $pdf->SetFont('DejaVuSans', 'B', 16);
            $pdf->Cell(0, 15, 'DISTRIBUȚIA ROLURILOR', 0, 1, 'C');
            $pdf->Cell(0, 10, 'Data generării: ' . date('d.m.Y H:i'), 0, 1, 'C');
            $pdf->Ln(10);
            
            // Header tabel
            $pdf->SetFont('DejaVuSans', 'B', 12);
            $pdf->SetFillColor(240, 240, 240);
            
            $pdf->Cell(80, 12, 'Rol', 1, 0, 'C', true);
            $pdf->Cell(40, 12, 'Număr Utilizatori', 1, 0, 'C', true);
            $pdf->Cell(60, 12, 'Procent (%)', 1, 1, 'C', true);
            
            // Calculează totalul pentru procente
            $totalUsers = array_sum(array_column($roleDistribution, 'numar_utilizatori'));
            
            // Datele
            $pdf->SetFont('DejaVuSans', '', 11);
            
            foreach ($roleDistribution as $index => $role) {
                $fill = ($index % 2 == 0) ? false : true;
                if ($fill) {
                    $pdf->SetFillColor(248, 248, 248);
                } else {
                    $pdf->SetFillColor(255, 255, 255);
                }
                
                $percent = $totalUsers > 0 ? round(($role['numar_utilizatori'] / $totalUsers) * 100, 1) : 0;
                
                $pdf->Cell(80, 10, $role['nume_rol'] ?? 'Fără rol', 1, 0, 'L', $fill);
                $pdf->Cell(40, 10, $role['numar_utilizatori'], 1, 0, 'C', $fill);
                $pdf->Cell(60, 10, $percent . '%', 1, 1, 'C', $fill);
            }
            
            // Total
            $pdf->SetFont('DejaVuSans', 'B', 11);
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Cell(80, 10, 'TOTAL', 1, 0, 'C', true);
            $pdf->Cell(40, 10, $totalUsers, 1, 0, 'C', true);
            $pdf->Cell(60, 10, '100%', 1, 1, 'C', true);
            
            $filename = 'distributie_roluri_' . date('Y-m-d_H-i-s') . '.pdf';
            $pdf->Output($filename, 'D');
            
        } catch (Exception $e) {
            error_log("Eroare export distribuție roluri PDF: " . $e->getMessage());
            $_SESSION['error'] = 'Eroare la exportul distribuției rolurilor: ' . $e->getMessage();
            header('Location: /admin/exports');
        }
    }

    /**
     * Exportă membrii proiectelor în PDF
     */
    public function exportProjectMembersPDF() {
        if (!$this->checkAdminAccess()) return;

        try {
            $projectMembers = $this->projectMemberModel->getAllWithDetails();
            
            $pdf = new \TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
            
            $pdf->SetCreator('Casa de Producție Filme');
            $pdf->SetAuthor('Administrator');
            $pdf->SetTitle('Membri Proiecte');
            
            $pdf->SetMargins(10, 20, 10);
            $pdf->SetAutoPageBreak(true, 25);
            
            $pdf->SetFont('DejaVuSans', '', 10);
            $pdf->AddPage();
            
            // Titlul
            $pdf->SetFont('DejaVuSans', 'B', 16);
            $pdf->Cell(0, 15, 'MEMBRI PROIECTE', 0, 1, 'C');
            $pdf->Cell(0, 10, 'Data generării: ' . date('d.m.Y H:i'), 0, 1, 'C');
            $pdf->Ln(10);
            
            // Header tabel
            $pdf->SetFont('DejaVuSans', 'B', 9);
            $pdf->SetFillColor(240, 240, 240);
            
            $pdf->Cell(60, 8, 'Proiect', 1, 0, 'C', true);
            $pdf->Cell(40, 8, 'Membru', 1, 0, 'C', true);
            $pdf->Cell(50, 8, 'Email', 1, 0, 'C', true);
            $pdf->Cell(40, 8, 'Rol', 1, 0, 'C', true);
            $pdf->Cell(25, 8, 'Data Adăugare', 1, 0, 'C', true);
            $pdf->Cell(20, 8, 'Status', 1, 0, 'C', true);
            $pdf->Cell(20, 8, 'Activ', 1, 1, 'C', true);
            
            // Datele
            $pdf->SetFont('DejaVuSans', '', 8);
            
            foreach ($projectMembers as $index => $member) {
                $fill = ($index % 2 == 0) ? false : true;
                if ($fill) {
                    $pdf->SetFillColor(248, 248, 248);
                } else {
                    $pdf->SetFillColor(255, 255, 255);
                }
                
                $pdf->Cell(60, 6, $this->truncateText($member['project_title'] ?? 'N/A', 30), 1, 0, 'L', $fill);
                $pdf->Cell(40, 6, $this->truncateText($member['member_name'] ?? 'N/A', 20), 1, 0, 'L', $fill);
                $pdf->Cell(50, 6, $this->truncateText($member['member_email'] ?? 'N/A', 25), 1, 0, 'L', $fill);
                $pdf->Cell(40, 6, $this->truncateText($member['role_in_project'] ?? 'N/A', 20), 1, 0, 'L', $fill);
                $pdf->Cell(25, 6, $member['joined_at'] ? date('d.m.Y', strtotime($member['joined_at'])) : 'N/A', 1, 0, 'C', $fill);
                $pdf->Cell(20, 6, $member['member_status'] == 'Activ' ? 'Da' : 'Nu', 1, 1, 'C', $fill);
            }
            
            $filename = 'membri_proiecte_' . date('Y-m-d_H-i-s') . '.pdf';
            $pdf->Output($filename, 'D');
            
        } catch (Exception $e) {
            error_log("Eroare export membri proiecte PDF: " . $e->getMessage());
            $_SESSION['error'] = 'Eroare la exportul membrilor proiectelor: ' . $e->getMessage();
            header('Location: /admin/exports');
        }
    }

    /**
     * Exportă toate datele într-o arhivă ZIP
     */
    public function exportAllAsArchive() {
        if (!$this->checkAdminAccess()) return;

        try {
            // Verifică dacă extensia ZIP este disponibilă
            if (!class_exists('ZipArchive')) {
                throw new \Exception('Extensia ZIP nu este disponibilă pe server.');
            }

            // Creează un director temporar
            $tempDir = sys_get_temp_dir() . '/export_' . time();
            if (!mkdir($tempDir)) {
                throw new \Exception('Nu s-a putut crea directorul temporar.');
            }

            // Generează toate tipurile de export PDF în directorul temporar
            $this->generateProjectsPDF($tempDir . '/proiecte_complete.pdf');
            $this->generateProjectsByCategoryPDF($tempDir . '/proiecte_categorii.pdf');
            $this->generateUsersPDF($tempDir . '/utilizatori.pdf');
            $this->generateRoleDistributionPDF($tempDir . '/distributie_roluri.pdf');
            $this->generateProjectMembersPDF($tempDir . '/membri_proiecte.pdf');

            // Creează arhiva ZIP
            $zipFile = sys_get_temp_dir() . '/export_complet_' . date('Y-m-d_H-i-s') . '.zip';
            $zip = new \ZipArchive();
            
            if ($zip->open($zipFile, \ZipArchive::CREATE) !== TRUE) {
                throw new \Exception('Nu s-a putut crea arhiva ZIP');
            }

            // Adaugă toate fișierele în arhivă
            $files = glob($tempDir . '/*.pdf');
            foreach ($files as $file) {
                if (file_exists($file)) {
                    $zip->addFile($file, basename($file));
                }
            }

            // Adaugă un fișier README
            $readmeContent = "Export complet - Casa de Producție Filme\n";
            $readmeContent .= "Generat la: " . date('Y-m-d H:i:s') . "\n\n";
            $readmeContent .= "Fișiere incluse:\n";
            $readmeContent .= "- proiecte_complete.pdf: Lista completă de proiecte\n";
            $readmeContent .= "- proiecte_categorii.pdf: Proiecte grupate pe categorii\n";
            $readmeContent .= "- utilizatori.pdf: Lista utilizatorilor cu roluri\n";
            $readmeContent .= "- distributie_roluri.pdf: Statistici distribuție roluri\n";
            $readmeContent .= "- membri_proiecte.pdf: Membri pe proiecte\n\n";
            $readmeContent .= "Toate fișierele sunt în format PDF.\n";
            
            $zip->addFromString('README.txt', $readmeContent);
            $zip->close();

            // Șterge fișierele temporare
            foreach ($files as $file) {
                if (file_exists($file)) {
                    unlink($file);
                }
            }
            rmdir($tempDir);

            // Trimite arhiva pentru download
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename=export_complet_' . date('Y-m-d_H-i-s') . '.zip');
            header('Content-Length: ' . filesize($zipFile));
            header('Pragma: no-cache');
            header('Expires: 0');

            readfile($zipFile);
            unlink($zipFile);

            exit;
            
        } catch (\Exception $e) {
            error_log("Eroare export arhivă: " . $e->getMessage());
            $_SESSION['error'] = 'Eroare la generarea exportului complet: ' . $e->getMessage();
            header('Location: /admin/exports');
            exit;
        }
    }

    /**
     * Afișează pagina de export
     */
    public function showExportPage() {
        if (!$this->checkAdminAccess()) return;

        // Obține statistici pentru afișare
        $stats = [
            'total_proiecte' => $this->projectModel->getTotalCount(),
            'total_utilizatori' => $this->userModel->getTotalCount(),
            'proiecte_active' => $this->projectModel->getActiveCount(),
            'utilizatori_activi' => $this->userModel->getActiveCount()
        ];

        require_once __DIR__ . '/../Views/admin/exports.php';
    }

    /**
     * Verifică dacă utilizatorul este admin
     */
    private function checkAdminAccess() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        if (!in_array('Admin', $_SESSION['roles'] ?? [])) {
            $_SESSION['error'] = 'Acces interzis. Doar administratorii pot exporta date.';
            header('Location: /');
            exit;
        }

        return true;
    }

    /**
     * Helper method pentru truncarea textului
     */
    private function truncateText($text, $maxLength) {
        if (strlen($text) <= $maxLength) {
            return $text;
        }
        return substr($text, 0, $maxLength - 3) . '...';
    }

    /**
     * Metodă helper pentru generarea PDF-ului proiectelor
     */
    private function generateProjectsPDF($filePath) {
        $projects = $this->projectModel->getAllWithDetails();
        
        $pdf = new \TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('Casa de Producție Filme');
        $pdf->SetTitle('Proiecte Complete');
        $pdf->SetMargins(10, 20, 10);
        $pdf->SetAutoPageBreak(true, 25);
        $pdf->SetFont('DejaVuSans', '', 10);
        $pdf->AddPage();
        
        // Titlul
        $pdf->SetFont('DejaVuSans', 'B', 16);
        $pdf->Cell(0, 15, 'RAPORT COMPLET PROIECTE', 0, 1, 'C');
        $pdf->Ln(10);
        
        // Header tabel
        $pdf->SetFont('DejaVuSans', 'B', 8);
        $pdf->SetFillColor(240, 240, 240);
        
        $pdf->Cell(15, 8, 'ID', 1, 0, 'C', true);
        $pdf->Cell(45, 8, 'Titlu', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'Tip', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'Status', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'Buget', 1, 0, 'C', true);
        $pdf->Cell(20, 8, 'Membri', 1, 1, 'C', true);
        
        // Datele
        $pdf->SetFont('DejaVuSans', '', 7);
        foreach ($projects as $index => $project) {
            $fill = ($index % 2 == 0) ? false : true;
            if ($fill) $pdf->SetFillColor(248, 248, 248);
            else $pdf->SetFillColor(255, 255, 255);
            
            $pdf->Cell(15, 6, $project['id'], 1, 0, 'C', $fill);
            $pdf->Cell(45, 6, $this->truncateText($project['title'], 25), 1, 0, 'L', $fill);
            $pdf->Cell(25, 6, $this->truncateText($project['tip'] ?? 'N/A', 15), 1, 0, 'L', $fill);
            $pdf->Cell(25, 6, $this->truncateText($project['status_name'] ?? 'N/A', 15), 1, 0, 'L', $fill);
            $pdf->Cell(30, 6, number_format($project['buget'] ?? 0, 0), 1, 0, 'R', $fill);
            $pdf->Cell(20, 6, $project['nr_membri'] ?? 0, 1, 1, 'C', $fill);
        }
        
        $pdf->Output($filePath, 'F');
    }

    /**
     * Metodă helper pentru generarea PDF-ului proiectelor pe categorii
     */
    private function generateProjectsByCategoryPDF($filePath) {
        $categories = $this->projectModel->getProjectsByCategory();
        
        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('Casa de Producție Filme');
        $pdf->SetTitle('Proiecte pe Categorii');
        $pdf->SetMargins(15, 20, 15);
        $pdf->SetAutoPageBreak(true, 25);
        $pdf->AddPage();
        
        $pdf->SetFont('DejaVuSans', 'B', 16);
        $pdf->Cell(0, 15, 'PROIECTE PE CATEGORII', 0, 1, 'C');
        $pdf->Ln(10);
        
        $pdf->SetFont('DejaVuSans', 'B', 10);
        $pdf->SetFillColor(240, 240, 240);
        
        $pdf->Cell(50, 10, 'Categorie', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Nr. Proiecte', 1, 0, 'C', true);
        $pdf->Cell(40, 10, 'Buget Total', 1, 0, 'C', true);
        $pdf->Cell(40, 10, 'Buget Mediu', 1, 1, 'C', true);
        
        $pdf->SetFont('DejaVuSans', '', 9);
        foreach ($categories as $index => $category) {
            $fill = ($index % 2 == 0) ? false : true;
            if ($fill) $pdf->SetFillColor(248, 248, 248);
            else $pdf->SetFillColor(255, 255, 255);
            
            $pdf->Cell(50, 8, $category['tip'] ?: 'Necategorizat', 1, 0, 'L', $fill);
            $pdf->Cell(30, 8, $category['numar_proiecte'], 1, 0, 'C', $fill);
            $pdf->Cell(40, 8, number_format($category['buget_total'] ?? 0, 0), 1, 0, 'R', $fill);
            $pdf->Cell(40, 8, number_format($category['buget_mediu'] ?? 0, 0), 1, 1, 'R', $fill);
        }
        
        $pdf->Output($filePath, 'F');
    }

    /**
     * Metodă helper pentru generarea PDF-ului utilizatorilor
     */
    private function generateUsersPDF($filePath) {
        $users = $this->userModel->getAllWithRoles();
        
        $pdf = new \TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('Casa de Producție Filme');
        $pdf->SetTitle('Utilizatori cu Roluri');
        $pdf->SetMargins(10, 20, 10);
        $pdf->SetAutoPageBreak(true, 25);
        $pdf->AddPage();
        
        $pdf->SetFont('DejaVuSans', 'B', 16);
        $pdf->Cell(0, 15, 'UTILIZATORI CU ROLURI', 0, 1, 'C');
        $pdf->Ln(10);
        
        $pdf->SetFont('DejaVuSans', 'B', 9);
        $pdf->SetFillColor(240, 240, 240);
        
        $pdf->Cell(20, 8, 'ID', 1, 0, 'C', true);
        $pdf->Cell(50, 8, 'Email', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'Nume', 1, 0, 'C', true);
        $pdf->Cell(40, 8, 'Roluri', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'Nr. Proiecte', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'Status', 1, 1, 'C', true);
        
        $pdf->SetFont('DejaVuSans', '', 8);
        foreach ($users as $index => $user) {
            $fill = ($index % 2 == 0) ? false : true;
            if ($fill) $pdf->SetFillColor(248, 248, 248);
            else $pdf->SetFillColor(255, 255, 255);
            
            $pdf->Cell(20, 6, $user['id'], 1, 0, 'C', $fill);
            $pdf->Cell(50, 6, $this->truncateText($user['email'], 25), 1, 0, 'L', $fill);
            $pdf->Cell(30, 6, $this->truncateText(($user['prenume'] ?? '') . ' ' . ($user['nume_familie'] ?? ''), 15), 1, 0, 'L', $fill);
            $pdf->Cell(40, 6, $this->truncateText($user['toate_rolurile'] ?? 'Fără rol', 20), 1, 0, 'L', $fill);
            $pdf->Cell(30, 6, $user['nr_proiecte_active'] ?? 0, 1, 0, 'C', $fill);
            $pdf->Cell(25, 6, $user['deleted_at'] ? 'Inactiv' : 'Activ', 1, 1, 'C', $fill);
        }
        
        $pdf->Output($filePath, 'F');
    }

    /**
     * Metodă helper pentru generarea PDF-ului distribuției rolurilor
     */
    private function generateRoleDistributionPDF($filePath) {
        $roles = $this->userModel->getRoleDistribution();
        
        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('Casa de Producție Filme');
        $pdf->SetTitle('Distribuția Rolurilor');
        $pdf->SetMargins(15, 20, 15);
        $pdf->SetAutoPageBreak(true, 25);
        $pdf->AddPage();
        
        $pdf->SetFont('DejaVuSans', 'B', 16);
        $pdf->Cell(0, 15, 'DISTRIBUȚIA ROLURILOR', 0, 1, 'C');
        $pdf->Ln(10);
        
        $pdf->SetFont('DejaVuSans', 'B', 12);
        $pdf->SetFillColor(240, 240, 240);
        
        $pdf->Cell(80, 12, 'Rol', 1, 0, 'C', true);
        $pdf->Cell(40, 12, 'Nr. Utilizatori', 1, 0, 'C', true);
        $pdf->Cell(60, 12, 'Procent (%)', 1, 1, 'C', true);
        
        $totalUsers = array_sum(array_column($roles, 'numar_utilizatori'));
        
        $pdf->SetFont('DejaVuSans', '', 11);
        foreach ($roles as $index => $role) {
            $fill = ($index % 2 == 0) ? false : true;
            if ($fill) $pdf->SetFillColor(248, 248, 248);
            else $pdf->SetFillColor(255, 255, 255);
            
            $percent = $totalUsers > 0 ? round(($role['numar_utilizatori'] / $totalUsers) * 100, 1) : 0;
            
            $pdf->Cell(80, 10, $role['rol'] ?? 'Fără rol', 1, 0, 'L', $fill);
            $pdf->Cell(40, 10, $role['numar_utilizatori'], 1, 0, 'C', $fill);
            $pdf->Cell(60, 10, $percent . '%', 1, 1, 'C', $fill);
        }
        
        $pdf->Output($filePath, 'F');
    }

    /**
     * Metodă helper pentru generarea PDF-ului membrilor proiectelor
     */
    private function generateProjectMembersPDF($filePath) {
        $members = $this->projectMemberModel->getAllWithDetails();
        
        $pdf = new \TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('Casa de Producție Filme');
        $pdf->SetTitle('Membri Proiecte');
        $pdf->SetMargins(10, 20, 10);
        $pdf->SetAutoPageBreak(true, 25);
        $pdf->AddPage();
        
        $pdf->SetFont('DejaVuSans', 'B', 16);
        $pdf->Cell(0, 15, 'MEMBRI PROIECTE', 0, 1, 'C');
        $pdf->Cell(0, 10, 'Data generării: ' . date('d.m.Y H:i'), 0, 1, 'C');
        $pdf->Ln(10);
        
        $pdf->SetFont('DejaVuSans', 'B', 9);
        $pdf->SetFillColor(240, 240, 240);
        
        $pdf->Cell(60, 8, 'Proiect', 1, 0, 'C', true);
        $pdf->Cell(40, 8, 'Membru', 1, 0, 'C', true);
        $pdf->Cell(50, 8, 'Email', 1, 0, 'C', true);
        $pdf->Cell(40, 8, 'Rol', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'Data Adăugare', 1, 0, 'C', true);
        $pdf->Cell(20, 8, 'Status', 1, 1, 'C', true);
        
        $pdf->SetFont('DejaVuSans', '', 8);
        foreach ($members as $index => $member) {
            $fill = ($index % 2 == 0) ? false : true;
            if ($fill) $pdf->SetFillColor(248, 248, 248);
            else $pdf->SetFillColor(255, 255, 255);
            
            $pdf->Cell(60, 6, $this->truncateText($member['project_title'] ?? 'N/A', 30), 1, 0, 'L', $fill);
            $pdf->Cell(40, 6, $this->truncateText($member['member_name'] ?? 'N/A', 20), 1, 0, 'L', $fill);
            $pdf->Cell(50, 6, $this->truncateText($member['member_email'] ?? 'N/A', 25), 1, 0, 'L', $fill);
            $pdf->Cell(40, 6, $this->truncateText($member['role_in_project'] ?? 'N/A', 20), 1, 0, 'L', $fill);
            $pdf->Cell(25, 6, $member['joined_at'] ? date('d.m.Y', strtotime($member['joined_at'])) : 'N/A', 1, 0, 'C', $fill);
            $pdf->Cell(20, 6, $member['member_status'] == 'Activ' ? 'Da' : 'Nu', 1, 1, 'C', $fill);
        }
        
        $pdf->Output($filePath, 'F');
    }
}
