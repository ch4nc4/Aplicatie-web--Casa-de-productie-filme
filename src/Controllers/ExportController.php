<?php

namespace App\Controllers;

require_once __DIR__ . '/../Models/Project.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/ProjectMember.php';

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
     * Exportă lista completă de proiecte în Excel
     */
    public function exportProjectsExcel() {
        if (!$this->checkAdminAccess()) return;

        try {
            $projects = $this->projectModel->getAllWithDetails();
            
            $filename = 'proiecte_complete_' . date('Y-m-d_H-i-s') . '.csv';
            
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=' . $filename);
            header('Pragma: no-cache');
            header('Expires: 0');
            
            // Adaugă BOM pentru UTF-8
            echo "\xEF\xBB\xBF";
            
            $output = fopen('php://output', 'w');
            
            // Header-ul tabelului
            fputcsv($output, [
                'ID',
                'Titlu Proiect',
                'Tip',
                'Status',
                'Buget (RON)',
                'Data Început',
                'Data Sfârșit',
                'Durată (zile)',
                'Descriere',
                'Nr. Membri Echipă',
                'Data Creare',
                'Ultima Actualizare'
            ]);
            
            // Datele
            foreach ($projects as $project) {
                $startDate = $project['data_inceput'] ? new \DateTime($project['data_inceput']) : null;
                $endDate = $project['data_sfarsit'] ? new \DateTime($project['data_sfarsit']) : null;
                $duration = ($startDate && $endDate) ? $startDate->diff($endDate)->days : 'N/A';
                
                fputcsv($output, [
                    $project['id'],
                    $project['title'],
                    $project['tip'] ?? 'Nespecificat',
                    $project['status'],
                    number_format($project['buget'] ?? 0, 2),
                    $project['data_inceput'] ?? 'N/A',
                    $project['data_sfarsit'] ?? 'N/A',
                    $duration,
                    strip_tags($project['descriere'] ?? ''),
                    $project['nr_membri'] ?? 0,
                    $project['created_at'] ?? 'N/A',
                    $project['updated_at'] ?? 'N/A'
                ]);
            }
            
            fclose($output);
            
        } catch (Exception $e) {
            error_log("Eroare export proiecte Excel: " . $e->getMessage());
            $_SESSION['error'] = 'Eroare la exportul proiectelor: ' . $e->getMessage();
            header('Location: /admin/exports');
        }
    }

    /**
     * Exportă proiecte pe categorii în Excel
     */
    public function exportProjectsByCategoryExcel() {
        if (!$this->checkAdminAccess()) return;

        try {
            $projectsByCategory = $this->projectModel->getProjectsByCategory();
            
            $filename = 'proiecte_categorii_' . date('Y-m-d_H-i-s') . '.csv';
            
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=' . $filename);
            header('Pragma: no-cache');
            header('Expires: 0');
            
            echo "\xEF\xBB\xBF";
            $output = fopen('php://output', 'w');
            
            // Header
            fputcsv($output, [
                'Categorie/Tip',
                'Număr Proiecte',
                'Buget Total (RON)',
                'Buget Mediu (RON)',
                'Status Predominant'
            ]);
            
            foreach ($projectsByCategory as $category) {
                fputcsv($output, [
                    $category['tip'] ?: 'Necategorizat',
                    $category['numar_proiecte'],
                    number_format($category['buget_total'] ?? 0, 2),
                    number_format($category['buget_mediu'] ?? 0, 2),
                    $category['status_predominant'] ?? 'N/A'
                ]);
            }
            
            fclose($output);
            
        } catch (Exception $e) {
            error_log("Eroare export categorii: " . $e->getMessage());
            $_SESSION['error'] = 'Eroare la exportul pe categorii: ' . $e->getMessage();
            header('Location: /admin/exports');
        }
    }

    /**
     * Exportă utilizatori cu roluri în Excel
     */
    public function exportUsersExcel() {
        if (!$this->checkAdminAccess()) return;

        try {
            $users = $this->userModel->getAllWithRoles();
            
            $filename = 'utilizatori_roluri_' . date('Y-m-d_H-i-s') . '.csv';
            
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=' . $filename);
            header('Pragma: no-cache');
            header('Expires: 0');
            
            echo "\xEF\xBB\xBF";
            $output = fopen('php://output', 'w');
            
            // Header
            fputcsv($output, [
                'ID Utilizator',
                'Nume Complet',
                'Email',
                'Username',
                'Rol Principal',
                'Toate Rolurile',
                'Nr. Proiecte Active',
                'Data Înregistrare',
                'Status Cont'
            ]);
            
            foreach ($users as $user) {
                fputcsv($output, [
                    $user['id'],
                    trim($user['prenume'] . ' ' . $user['nume_familie']),
                    $user['email'],
                    $user['username'] ?? 'N/A',
                    $user['rol_principal'] ?? 'Utilizator',
                    $user['toate_rolurile'] ?? 'Fără rol',
                    $user['nr_proiecte_active'] ?? 0,
                    $user['created_at'] ?? 'N/A',
                    $user['deleted_at'] ? 'Inactiv' : 'Activ'
                ]);
            }
            
            fclose($output);
            
        } catch (Exception $e) {
            error_log("Eroare export utilizatori: " . $e->getMessage());
            $_SESSION['error'] = 'Eroare la exportul utilizatorilor: ' . $e->getMessage();
            header('Location: /admin/exports');
        }
    }

    /**
     * Exportă distribuția rolurilor
     */
    public function exportRoleDistributionExcel() {
        if (!$this->checkAdminAccess()) return;

        try {
            $roleDistribution = $this->userModel->getRoleDistribution();
            
            $filename = 'distributie_roluri_' . date('Y-m-d_H-i-s') . '.csv';
            
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=' . $filename);
            header('Pragma: no-cache');
            header('Expires: 0');
            
            echo "\xEF\xBB\xBF";
            $output = fopen('php://output', 'w');
            
            // Header
            fputcsv($output, [
                'Rol',
                'Număr Utilizatori',
                'Procent din Total (%)'
            ]);
            
            foreach ($roleDistribution as $role) {
                fputcsv($output, [
                    $role['rol'] ?: 'Fără rol',
                    $role['numar_utilizatori'],
                    number_format($role['procent_total'], 2)
                ]);
            }
            
            fclose($output);
            
        } catch (Exception $e) {
            error_log("Eroare export distribuție roluri: " . $e->getMessage());
            $_SESSION['error'] = 'Eroare la exportul distribuției rolurilor: ' . $e->getMessage();
            header('Location: /admin/exports');
        }
    }

    /**
     * Exportă membri pe proiecte
     */
    public function exportProjectMembersExcel() {
        if (!$this->checkAdminAccess()) return;

        try {
            $projectMembers = $this->projectMemberModel->getAllWithDetails();
            
            $filename = 'membri_proiecte_' . date('Y-m-d_H-i-s') . '.csv';
            
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=' . $filename);
            header('Pragma: no-cache');
            header('Expires: 0');
            
            echo "\xEF\xBB\xBF";
            $output = fopen('php://output', 'w');
            
            // Header
            fputcsv($output, [
                'Proiect',
                'Tip Proiect',
                'Status Proiect',
                'Nume Membru',
                'Email Membru',
                'Data Alăturare',
                'Status Membru',
                'Contributii/Observații'
            ]);
            
            foreach ($projectMembers as $member) {
                fputcsv($output, [
                    $member['project_title'],
                    $member['project_type'] ?? 'N/A',
                    $member['project_status'],
                    trim($member['member_name']),
                    $member['member_email'],
                    $member['joined_at'] ?? 'N/A',
                    $member['member_status'] ?? 'Activ'
                ]);
            }
            
            fclose($output);
            
        } catch (Exception $e) {
            error_log("Eroare export membri proiecte: " . $e->getMessage());
            $_SESSION['error'] = 'Eroare la exportul membrilor: ' . $e->getMessage();
            header('Location: /admin/exports');
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
     * Metodă helper pentru generarea CSV-ului proiectelor
     */
    private function generateProjectsCSV($filePath) {
        $projects = $this->projectModel->getAllWithDetails();
        $output = fopen($filePath, 'w');
        
        // Adaugă BOM pentru UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($output, [
            'ID', 'Titlu Proiect', 'Tip', 'Status', 'Buget (RON)',
            'Data Început', 'Data Sfârșit', 'Durată (zile)',
            'Nr. Membri', 'Descriere', 'Creat la', 'Actualizat la'
        ]);
        
        foreach ($projects as $project) {
            $startDate = $project['data_inceput'] ?? '';
            $endDate = $project['data_sfarsit'] ?? '';
            $duration = '';
            
            if ($startDate && $endDate) {
                $start = new DateTime($startDate);
                $end = new DateTime($endDate);
                $duration = $start->diff($end)->days;
            }
            
            fputcsv($output, [
                $project['id'] ?? '',
                $project['nume_proiect'] ?? '',
                $project['tip'] ?? '',
                $project['status_name'] ?? 'Nedefinit',
                number_format($project['buget'] ?? 0, 2),
                $startDate,
                $endDate,
                $duration,
                $project['nr_membri'] ?? 0,
                substr($project['descriere'] ?? '', 0, 100) . '...',
                $project['created_at'] ?? '',
                $project['updated_at'] ?? ''
            ]);
        }
        
        fclose($output);
    }

    /**
     * Metodă helper pentru generarea CSV-ului proiectelor pe categorii
     */
    private function generateProjectsByCategoryCSV($filePath) {
        $categories = $this->projectModel->getProjectsByCategory();
        $output = fopen($filePath, 'w');
        
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($output, [
            'Categorie/Tip', 'Nr. Proiecte', 'Buget Total (RON)',
            'Buget Mediu (RON)', 'Status Predominant'
        ]);
        
        foreach ($categories as $category) {
            fputcsv($output, [
                $category['tip'] ?? 'Nedefinit',
                $category['nr_proiecte'] ?? 0,
                number_format($category['buget_total'] ?? 0, 2),
                number_format($category['buget_mediu'] ?? 0, 2),
                $category['status_predominant'] ?? 'Nedefinit'
            ]);
        }
        
        fclose($output);
    }

    /**
     * Metodă helper pentru generarea CSV-ului utilizatorilor
     */
    private function generateUsersCSV($filePath) {
        $users = $this->userModel->getAllWithRoles();
        $output = fopen($filePath, 'w');
        
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($output, [
            'ID', 'Nume', 'Prenume', 'Email', 'Roluri',
            'Nr. Proiecte Active', 'Ultima Activitate', 'Status Cont', 'Data Înregistrare'
        ]);
        
        foreach ($users as $user) {
            fputcsv($output, [
                $user['id'] ?? '',
                $user['nume'] ?? '',
                $user['prenume'] ?? '',
                $user['email'] ?? '',
                $user['roluri'] ?? 'Fără rol',
                $user['nr_proiecte_active'] ?? 0,
                $user['ultima_activitate'] ?? 'Niciodată',
                $user['status_cont'] ?? 'Activ',
                $user['created_at'] ?? ''
            ]);
        }
        
        fclose($output);
    }

    /**
     * Metodă helper pentru generarea CSV-ului distribuției rolurilor
     */
    private function generateRoleDistributionCSV($filePath) {
        $roles = $this->userModel->getRoleDistribution();
        $output = fopen($filePath, 'w');
        
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($output, [
            'Nume Rol', 'Nr. Utilizatori', 'Procent din Total (%)',
            'Utilizatori Activi', 'Utilizatori Inactivi'
        ]);
        
        foreach ($roles as $role) {
            fputcsv($output, [
                $role['nume_rol'] ?? '',
                $role['nr_utilizatori'] ?? 0,
                number_format($role['procent'] ?? 0, 2),
                $role['utilizatori_activi'] ?? 0,
                $role['utilizatori_inactivi'] ?? 0
            ]);
        }
        
        fclose($output);
    }

    /**
     * Metodă helper pentru generarea CSV-ului membrilor de proiecte
     */
    private function generateProjectMembersCSV($filePath) {
        $members = $this->projectMemberModel->getAllWithDetails();
        $output = fopen($filePath, 'w');
        
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($output, [
            'Nume Proiect', 'Tip Proiect', 'Nume Membru', 'Email Membru',
            'Rol în Proiect', 'Data Alăturare', 'Status', 'Observații'
        ]);
        
        foreach ($members as $member) {
            fputcsv($output, [
                $member['nume_proiect'] ?? '',
                $member['tip_proiect'] ?? '',
                ($member['nume'] ?? '') . ' ' . ($member['prenume'] ?? ''),
                $member['email'] ?? '',
                $member['rol'] ?? 'Nedefinit',
                $member['data_alaturat'] ?? '',
                $member['status'] ?? 'Activ',
                $member['observatii'] ?? ''
            ]);
        }
        
        fclose($output);
    }
}
