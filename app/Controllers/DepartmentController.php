<?php

namespace Controllers;

use Core\Session;
use Models\Department;
use Dompdf\Dompdf;

class DepartmentController{

    public function index() {

        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        $cedula = Session::get('cedula');
        $last_connection_raw = Session::get('last_connection');
        $last_connection = !empty($last_connection_raw) ? date('d/m/Y - h:i a', strtotime($last_connection_raw)) : '';

        $departamentos = Department::directory();

        $userRoles = Session::get('user_roles') ?? [];

        require_once __DIR__ . '/../View/departments/index.php';
    }

    public function exportPdf() {

        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        $cedula = Session::get('cedula');

        $departamentos = Department::directory();
        $totalRecords = count($departamentos);

        $generatedBy = "C.I: " . htmlspecialchars($cedula ?? '---');
        $dateTime = date('d/m/Y - h:i A');

        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Directorio de Departamentos - UPTVal</title>
            <style>
                body { font-family: DejaVu Sans, sans-serif; font-size: 10pt; color: #1e293b; margin: 0; padding: 20px; }
                .header { text-align: center; border-bottom: 2px solid #d97b29; padding-bottom: 12px; margin-bottom: 20px; }
                .header h1 { margin: 0; font-size: 16pt; color: #0f172a; }
                .header h1 span { color: #d97b29; }
                .header p { margin: 4px 0 0; font-size: 8pt; color: #64748b; }
                .meta { font-size: 8pt; color: #475569; margin-bottom: 16px; }
                .meta span { display: inline-block; margin-right: 24px; }
                .record-count { font-size: 8pt; font-weight: 600; color: #0f172a; margin-bottom: 4px; }
                table { width: 100%; border-collapse: collapse; }
                th { background-color: #0f172a; color: #ffffff; padding: 8px 10px; text-align: left; font-size: 9pt; text-transform: uppercase; letter-spacing: 0.5px; }
                td { padding: 7px 10px; border-bottom: 1px solid #e2e8f0; font-size: 9pt; }
                tr:nth-child(even) td { background-color: #f8fafc; }
                .badge { display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 8pt; font-weight: 600; }
                .badge-active { background-color: #dcfce7; color: #15803d; }
                .badge-inactive { background-color: #fee2e2; color: #b91c1c; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1><span>UPT</span>Val</h1>
                <p>Universidad Politecnica Territorial de Valencia</p>
            </div>
            <div class="meta">
                <span><strong>Generado por:</strong> ' . $generatedBy . '</span>
                <span><strong>Fecha:</strong> ' . $dateTime . '</span>
                <span><strong>Total de departamentos:</strong> ' . $totalRecords . '</span>
            </div>
            <div class="record-count">Directorio General de Departamentos</div>
            <table>
                <thead>
                    <tr>
                        <th>Departamento</th>
                        <th>Estado</th>
                        <th>N. de Personal</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($departamentos as $depto) {
            $statusBadge = $depto['status'] === 'activo'
                ? '<span class="badge badge-active">Activo</span>'
                : '<span class="badge badge-inactive">Inactivo</span>';
            $html .= '<tr>
                <td>' . htmlspecialchars($depto['name']) . '</td>
                <td>' . $statusBadge . '</td>
                <td>' . (int) $depto['staff_count'] . '</td>
            </tr>';
        }

        $html .= '</tbody>
            </table>
            <div class="footer" style="margin-top:30px; font-size:8pt; color:#94a3b8; text-align:center;">Documento generado por el Sistema de Gestion Academica UPTVal.</div>
        </body>
        </html>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $canvas = $dompdf->getCanvas();
        $fontMetrics = $dompdf->getFontMetrics();
        $font = $fontMetrics->getFont('DejaVu Sans', 'normal');
        $canvas->page_script(function($pageNumber, $pageCount) use ($font, $fontMetrics, $canvas) {
            $text = "Pagina $pageNumber de $pageCount";
            $textWidth = $fontMetrics->getTextWidth($text, $font, 8);
            $x = ($canvas->get_width() - $textWidth) / 2;
            $canvas->text($x, $canvas->get_height() - 20, $text, $font, 8);
        });

        $dompdf->stream('directorio_departamentos_' . date('Ymd_His') . '.pdf', ['Attachment' => true]);
        exit;
    }

    public function exportPdfCoordinator() {

        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        $id = $_GET['id_department'] ?? null;

        if ($id === null || !ctype_digit((string) $id)) {
            $_SESSION['flash_message'] = [
                'type'    => 'error',
                'title'   => 'Registro no encontrado',
                'message' => 'El identificador del departamento no es válido.'
            ];
            header("Location: /departamentos");
            exit;
        }

        $depto = Department::findWithCoordinator((int) $id);

        if (!$depto) {
            $_SESSION['flash_message'] = [
                'type'    => 'error',
                'title'   => 'Registro no encontrado',
                'message' => 'El departamento solicitado no existe en la base de datos.'
            ];
            header("Location: /departamentos");
            exit;
        }

        $cedula = Session::get('cedula');
        $generatedBy = "C.I: " . htmlspecialchars($cedula ?? '---');
        $dateTime = date('d/m/Y - h:i A');

        $coordinator = !empty($depto['coordinator_cedula'])
            ? trim(($depto['coordinator_last_name'] ?? '') . ', ' . ($depto['coordinator_first_name'] ?? ''))
            : 'Sin coordinador asignado';
        $coordinatorCedula = $depto['coordinator_cedula'] ?? '---';
        $coordinatorDate = !empty($depto['coordinator_assignment_date'])
            ? date('d/m/Y', strtotime($depto['coordinator_assignment_date']))
            : '---';

        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Coordinador del Departamento - UPTVal</title>
            <style>
                body { font-family: DejaVu Sans, sans-serif; font-size: 10pt; color: #1e293b; margin: 0; padding: 30px; }
                .header { text-align: center; border-bottom: 2px solid #d97b29; padding-bottom: 12px; margin-bottom: 20px; }
                .header h1 { margin: 0; font-size: 18pt; color: #0f172a; }
                .header h1 span { color: #d97b29; }
                .header p { margin: 4px 0 0; font-size: 9pt; color: #64748b; }
                .title { text-align: center; font-size: 13pt; font-weight: bold; color: #0f172a; margin: 16px 0 20px; }
                .meta { font-size: 8pt; color: #475569; margin-bottom: 20px; }
                .meta span { display: inline-block; margin-right: 24px; }
                table.data { width: 100%; border-collapse: collapse; }
                table.data td { border: 1px solid #e2e8f0; padding: 9px 12px; font-size: 10pt; }
                table.data td.label { width: 35%; background-color: #f8fafc; color: #475569; font-weight: 600; text-transform: uppercase; font-size: 8pt; letter-spacing: 0.5px; }
                table.data td.value { color: #0f172a; font-weight: 600; }
                .footer { margin-top: 30px; font-size: 8pt; color: #94a3b8; text-align: center; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1><span>UPT</span>Val</h1>
                <p>Universidad Politecnica Territorial de Valencia</p>
            </div>
            <div class="meta">
                <span><strong>Generado por:</strong> ' . $generatedBy . '</span>
                <span><strong>Fecha:</strong> ' . $dateTime . '</span>
            </div>
            <div class="title">Coordinador del Departamento</div>
            <table class="data">
                <tr><td class="label">Departamento</td><td class="value">' . htmlspecialchars($depto['name']) . '</td></tr>
                <tr><td class="label">Coordinador</td><td class="value">' . htmlspecialchars($coordinator) . '</td></tr>
                <tr><td class="label">Cedula</td><td class="value">' . htmlspecialchars($coordinatorCedula) . '</td></tr>
                <tr><td class="label">Fecha de Asignacion</td><td class="value">' . $coordinatorDate . '</td></tr>
            </table>
            <div class="footer">Documento generado por el Sistema de Gestion Academica UPTVal.</div>
        </body>
        </html>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'coordinador_' . preg_replace('/[^A-Za-z0-9]/', '_', strtolower($depto['name'])) . '_' . date('Ymd_His') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }
}
