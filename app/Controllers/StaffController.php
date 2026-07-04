<?php

namespace Controllers;

use Core\Session;
use Models\Staff;
use Models\Department;
use Dompdf\Dompdf;

class StaffController{

/*
    public function index(){

        if(!Session::has('id_user')){
            header("Location: /login");
            exit;
        }

        $cedula = Session::get('cedula');

        $last_connection_raw = Session::get('last_connection');
        $last_connection = '';

        if (!empty($last_connection_raw)) {
            $last_connection = date('d/m/Y - h:i a', strtotime($last_connection_raw));
        }
        
        $staffList = Staff::all();
       
        if(!empty($staffList) && isset($staffList['id_staff'])){
            $staffList = [$staffList];
        }

        $limit = 10;
        // 2. Capturar la página actual desde la URL (ej. ?page=2). Si no existe, es la 1.
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
        if ($page < 1) {
            $page = 1;
        }

        $offset = ($page - 1) * $limit;

        $totalRecords = Staff::countAll();
        $totalPages = ceil($totalRecords / $limit); // Redondea hacia arriba

        // 5. Evitar que el usuario ponga una página que no existe en la URL
        if ($page > $totalPages && $totalPages > 0) {
            $page = $totalPages;
            $offset = ($page - 1) * $limit;
        }

        $staffList = Staff::paginate($limit, $offset);

        $departamentos = \Models\Department::all();

        /*echo "<pre style='background: #111; color: #0f0; padding: 20px; z-index: 9999; position: relative;'>";
            print_r($totalRecords);
        echo "</pre>";
        die();*//*

        require_once __DIR__ . '/../View/staff/index.php';
    }*/

        public function index() {

        if(!Session::has('id_user')){
            header("Location: /login");
            exit;
        }

        $cedula = Session::get('cedula');
        $last_connection_raw = Session::get('last_connection');
        $last_connection = !empty($last_connection_raw) ? date('d/m/Y - h:i a', strtotime($last_connection_raw)) : '';
        
        // 1. Capturar filtros desde GET
        $filters = [
            'id_department' => $_GET['department_filter'] ?? null,
            'type_staff'    => $_GET['type_filter'] ?? null,
            'status'        => $_GET['status_filter'] ?? null,
            'search'        => $_GET['search'] ?? null
        ];

        // 2. Lógica de Paginación
        $limit = 10;
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
        $page = ($page < 1) ? 1 : $page;
        $offset = ($page - 1) * $limit;

        // 3. Obtener el total de registros FILTRADOS
        $totalRecords = Staff::countFilter($filters); 
        $totalPages = ceil($totalRecords / $limit);

        if ($page > $totalPages && $totalPages > 0) {
            $page = $totalPages;
            $offset = ($page - 1) * $limit;
        }

        // 4. Llamar al nuevo método filter que creamos en el modelo
        $staffList = Staff::filter($filters, $limit, $offset);

        $departamentos = \Models\Department::all();
        $statsByType = Staff::getStatsByType($filters['id_department']);

        $userRoles = Session::get('user_roles') ?? [];

        require_once __DIR__ . '/../View/staff/index.php';
    }

    public function toggleStatus() {
        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        $idStaff = $_POST['id_staff'] ?? null;

        if ($idStaff && Staff::toggleStatus($idStaff)) {
            $newStatus = Staff::getStatus($idStaff);
            $msg = $newStatus === 'activo' ? 'Personal activado correctamente.' : 'Personal inactivado correctamente.';
            $_SESSION['flash_message'] = ['type' => 'success', 'title' => '¡Cambio de estado exitoso!', 'message' => $msg];
        }

        header("Location: /personal");
        exit;
    }

    public function exportPdf() {
        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        $cedula = Session::get('cedula');

        $filters = [
            'id_department' => $_GET['department_filter'] ?? null,
            'type_staff'    => $_GET['type_filter'] ?? null,
            'status'        => $_GET['status_filter'] ?? null,
            'search'        => $_GET['search'] ?? null
        ];

        $staffList = Staff::filter($filters);

        $rowsHtml = '';
        foreach ($staffList as $person) {
            $statusBadge = $person['status'] === 'activo'
                ? '<span style="color:#16a34a;font-weight:600;">Activo</span>'
                : '<span style="color:#dc2626;font-weight:600;">Inactivo</span>';
            $rowsHtml .= '<tr>
                <td>' . htmlspecialchars($person['cedula']) . '</td>
                <td>' . htmlspecialchars($person['last_name'] . ', ' . $person['first_name']) . '</td>
                <td>' . htmlspecialchars($person['pas']) . '</td>
                <td>' . htmlspecialchars($person['name'] ?? 'Sin Asignar') . '</td>
                <td>' . $statusBadge . '</td>
            </tr>';
        }

        $generatedBy = "C.I: " . htmlspecialchars($cedula ?? '---');
        $dateTime = date('d/m/Y - h:i A');

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Reporte de Personal - UPTVal</title>
            <style>
                body { font-family: DejaVu Sans, sans-serif; font-size: 10pt; color: #1e293b; margin: 0; padding: 20px; }
                .header { text-align: center; border-bottom: 2px solid #d97b29; padding-bottom: 12px; margin-bottom: 20px; }
                .header h1 { margin: 0; font-size: 16pt; color: #0f172a; }
                .header h1 span { color: #d97b29; }
                .header p { margin: 4px 0 0; font-size: 8pt; color: #64748b; }
                .meta { font-size: 8pt; color: #475569; margin-bottom: 16px; }
                .meta span { display: inline-block; margin-right: 24px; }
                table { width: 100%; border-collapse: collapse; margin-top: 8px; }
                th { background-color: #0f172a; color: #ffffff; padding: 8px 10px; text-align: left; font-size: 9pt; text-transform: uppercase; letter-spacing: 0.5px; }
                td { padding: 7px 10px; border-bottom: 1px solid #e2e8f0; font-size: 9pt; }
                tr:nth-child(even) td { background-color: #f8fafc; }
                .footer { text-align: center; margin-top: 24px; font-size: 7pt; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 12px; }
                .total { text-align: right; font-size: 9pt; font-weight: 600; margin-top: 12px; color: #0f172a; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1><span>UPT</span>Val</h1>
                <p>Universidad Politécnica Territorial de Valencia</p>
            </div>
            <div class="meta">
                <span><strong>Generado por:</strong> ' . $generatedBy . '</span>
                <span><strong>Fecha:</strong> ' . $dateTime . '</span>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Cédula</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Departamento</th>
                        <th>Estatus</th>
                    </tr>
                </thead>
                <tbody>
                    ' . $rowsHtml . '
                </tbody>
            </table>
            <div class="total">Total de registros: ' . count($staffList) . '</div>
            <div class="footer">Sistema de Gestión Académica UPTVal — Reporte generado el ' . $dateTime . '</div>
        </body>
        </html>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('personal_' . date('Ymd_His') . '.pdf', ['Attachment' => true]);
        exit;
    }
}