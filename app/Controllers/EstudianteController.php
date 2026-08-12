<?php

namespace Controllers;

use Core\Session;
use Models\Estudiante;
use Models\Trayecto;
use Models\Especialidad;
use Dompdf\Dompdf;

/**
 * Controlador de Estudiantes (solo lectura)
 * Los estudiantes se registran desde el sistema de inscripcion externo.
 */
class EstudianteController
{
    public function index()
    {
        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        $cedula = Session::get('cedula');
        $last_connection_raw = Session::get('last_connection');
        $last_connection = !empty($last_connection_raw) ? date('d/m/Y - h:i a', strtotime($last_connection_raw)) : '';
        $userRoles = Session::get('user_roles') ?? [];

        $filters = [
            'id_trayecto'     => $_GET['trayecto_filter'] ?? null,
            'id_especialidad' => $_GET['especialidad_filter'] ?? null,
            'status'          => $_GET['status_filter'] ?? null,
            'search'          => $_GET['search'] ?? null
        ];

        $limit = 10;
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
        $page = ($page < 1) ? 1 : $page;
        $offset = ($page - 1) * $limit;

        $totalRecords = Estudiante::countFilter($filters);
        $totalPages = ceil($totalRecords / $limit);

        if ($page > $totalPages && $totalPages > 0) {
            $page = $totalPages;
            $offset = ($page - 1) * $limit;
        }

        $estudiantes = Estudiante::filter($filters, $limit, $offset);
        $trayectos = Trayecto::all();
        $especialidades = Especialidad::all();

        require_once __DIR__ . '/../View/estudiantes/index.php';
    }

    public function exportPdf()
    {
        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        $cedula = Session::get('cedula');

        $filters = [
            'id_trayecto'     => $_GET['trayecto_filter'] ?? null,
            'id_especialidad' => $_GET['especialidad_filter'] ?? null,
            'status'          => $_GET['status_filter'] ?? null,
            'search'          => $_GET['search'] ?? null
        ];

        $estudiantesList = Estudiante::filter($filters);
        $totalRecords = count($estudiantesList);
        $perPage = 50;
        $chunks = array_chunk($estudiantesList, $perPage);
        $totalPages = count($chunks);

        $generatedBy = "C.I: " . htmlspecialchars($cedula ?? '---');
        $dateTime = date('d/m/Y - h:i A');

        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Reporte de Estudiantes - UPTVal</title>
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
                .page-break { page-break-after: always; }
                .page-info { text-align: center; font-size: 7pt; color: #94a3b8; margin-top: 16px; }
            </style>
        </head>
        <body>';

        foreach ($chunks as $index => $chunk) {
            if ($index > 0) {
                $html .= '<div class="page-break"></div>';
            }

            if ($index === 0) {
                $html .= '<div class="header">
                    <h1><span>UPT</span>Val</h1>
                    <p>Universidad Politecnica Territorial de Valencia</p>
                </div>
                <div class="meta">
                    <span><strong>Generado por:</strong> ' . $generatedBy . '</span>
                    <span><strong>Fecha:</strong> ' . $dateTime . '</span>
                    <span><strong>Total de registros:</strong> ' . $totalRecords . '</span>
                </div>';
            }

            $html .= '<table>
                <thead>
                    <tr>
                        <th>Cedula</th>
                        <th>Apellidos y Nombres</th>
                        <th>Trayecto</th>
                        <th>Especialidad</th>
                        <th>Seccion</th>
                        <th>Telefono</th>
                        <th>Email</th>
                        <th>Estatus</th>
                    </tr>
                </thead>
                <tbody>';

            foreach ($chunk as $est) {
                $trayecto = !empty($est['trayecto_name'])
                    ? htmlspecialchars($est['trayecto_name'])
                    : 'Sin asignar';
                $especialidad = !empty($est['especialidad_name'])
                    ? htmlspecialchars($est['especialidad_name'])
                    : 'Sin asignar';
                $statusBadge = ($est['status'] ?? 'activo') === 'activo'
                    ? 'Activo'
                    : 'Inactivo';
                $html .= '<tr>
                    <td>' . htmlspecialchars($est['cedula']) . '</td>
                    <td>' . htmlspecialchars($est['last_name'] . ', ' . $est['first_name']) . '</td>
                    <td>' . $trayecto . '</td>
                    <td>' . $especialidad . '</td>
                    <td>' . htmlspecialchars($est['seccion'] ?? '') . '</td>
                    <td>' . htmlspecialchars($est['phone'] ?? '') . '</td>
                    <td>' . htmlspecialchars($est['email'] ?? '') . '</td>
                    <td>' . $statusBadge . '</td>
                </tr>';
            }

            $html .= '</tbody>
            </table>';
        }

        $html .= '</body></html>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
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

        $dompdf->stream('estudiantes_' . date('Ymd_His') . '.pdf', ['Attachment' => true]);
        exit;
    }
}
