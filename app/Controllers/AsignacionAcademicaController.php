<?php

namespace Controllers;

use Core\Session;
use Models\Staff;
use Models\Materia;
use Models\Trayecto;
use Models\StaffMateria;
use Dompdf\Dompdf;

class AsignacionAcademicaController
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

        $filters = [
            'id_materia'   => $_GET['materia_filter'] ?? null,
            'id_trayecto'  => $_GET['trayecto_filter'] ?? null,
            'state'        => $_GET['status_filter'] ?? null,
            'search'       => $_GET['search'] ?? null
        ];

        $limit = 10;
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
        $page = ($page < 1) ? 1 : $page;
        $offset = ($page - 1) * $limit;

        $totalRecords = StaffMateria::countFilter($filters);
        $totalPages = ceil($totalRecords / $limit);

        if ($page > $totalPages && $totalPages > 0) {
            $page = $totalPages;
            $offset = ($page - 1) * $limit;
        }

        $assignments = StaffMateria::filter($filters, $limit, $offset);
        $materias = Materia::allActive();
        $trayectos = Trayecto::all();
        $userRoles = Session::get('user_roles') ?? [];

        require_once __DIR__ . '/../View/asignacion/index.php';
    }

    public function searchByCedula()
    {
        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        $cedula = trim($_GET['cedula'] ?? '');

        header('Content-Type: application/json');

        if ($cedula === '') {
            echo json_encode([
                'ok'      => false,
                'message' => 'Ingrese una cédula para realizar la búsqueda.'
            ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
            exit;
        }

        $staff = Staff::findByCedula($cedula);

        if (!$staff) {
            echo json_encode([
                'ok'      => false,
                'message' => 'No se encontró ningún staff registrado con esa cédula.'
            ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
            exit;
        }

        if (($staff['pas'] ?? '') !== 'Docente') {
            echo json_encode([
                'ok'      => false,
                'message' => 'El personal con esa cédula no es docente. Solo los docentes pueden recibir asignaciones académicas.'
            ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
            exit;
        }

        $assignments = StaffMateria::getActiveAssignmentsByStaff((int) $staff['id_staff']);
        $materiaIds = StaffMateria::getMateriaIdsByStaff((int) $staff['id_staff']);
        $availableMaterias = Materia::allActive();

        echo json_encode([
            'ok'                 => true,
            'staff'              => [
                'id_staff'        => (int) $staff['id_staff'],
                'cedula'          => $staff['cedula'],
                'first_name'      => $staff['first_name'],
                'last_name'       => $staff['last_name'],
                'department_name' => $staff['department_name'],
                'status'          => $staff['status']
            ],
            'assignments'        => $assignments,
            'materia_ids'        => $materiaIds,
            'has_assignment'     => count($assignments) > 0,
            'available_materias' => $availableMaterias
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        exit;
    }

    public function store()
    {
        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /personal/asignacion-academica");
            exit;
        }

        $idStaff   = $_POST['id_staff'] ?? null;
        $idMateria = $_POST['id_materia'] ?? null;

        if (!ctype_digit((string) $idStaff) || !ctype_digit((string) $idMateria)) {
            self::respondOrRedirect('error', 'Datos no válidos', 'Debe seleccionar un docente y una materia válidos.', '/personal/asignacion-academica');
        }

        $staff = Staff::findById((int) $idStaff);
        if (!$staff) {
            self::respondOrRedirect('error', 'Docente no encontrado', 'El docente seleccionado no existe en el sistema.', '/personal/asignacion-academica');
        }

        if (($staff['pas'] ?? '') !== 'Docente') {
            self::respondOrRedirect('error', 'Personal no asignable', 'Solo los docentes pueden recibir asignaciones de materias.', '/personal/asignacion-academica');
        }

        if ($staff['status'] !== 'activo' && $staff['status'] !== 'pendiente') {
            self::respondOrRedirect('error', 'Docente no asignable', 'El docente debe estar en estado activo o pendiente para recibir asignaciones.', '/personal/asignacion-academica');
        }

        $materia = Materia::findById((int) $idMateria);
        if (!$materia) {
            self::respondOrRedirect('error', 'Materia no encontrada', 'La materia seleccionada no existe en el sistema.', '/personal/asignacion-academica');
        }

        if ($materia['status'] !== 'activo') {
            self::respondOrRedirect('error', 'Materia inactiva', 'No se puede asignar una materia que se encuentra inactiva.', '/personal/asignacion-academica');
        }

        try {
            $result = StaffMateria::assign((int) $idStaff, (int) $idMateria);

            if ($result === 'assigned') {
                self::respondOrRedirect('success', 'Materia asignada', 'La materia se asignó correctamente al docente.', '/personal/asignacion-academica');
            } elseif ($result === 'reactivated') {
                self::respondOrRedirect('success', 'Materia reactivada', 'La asignación previa de la materia fue reactivada.', '/personal/asignacion-academica');
            } else {
                self::respondOrRedirect('error', 'Materia ya asignada', 'El docente ya cuenta con esa materia en estado activo.', '/personal/asignacion-academica');
            }
        } catch (\PDOException $e) {
            error_log("Error asignando materia: " . $e->getMessage());
            self::respondOrRedirect('error', 'Error al asignar', 'Ocurrió un error inesperado al asignar la materia.');
        }
    }

    public function delete()
    {
        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /personal/asignacion-academica");
            exit;
        }

        $id = $_POST['id_staff_materia'] ?? null;

        if (!ctype_digit((string) $id)) {
            self::respondOrRedirect('error', 'Asignación no válida', 'El identificador de la asignación no es válido.', '/personal/asignacion-academica');
        }

        $assignment = StaffMateria::findAssignment((int) $id);

        if (!$assignment) {
            self::respondOrRedirect('error', 'Asignación no encontrada', 'La asignación de materia solicitada no existe.', '/personal/asignacion-academica');
        }

        try {
            StaffMateria::delete((int) $id);
            self::respondOrRedirect('success', 'Asignación eliminada', 'La asignación de la materia fue eliminada correctamente.', '/personal/asignacion-academica');
        } catch (\PDOException $e) {
            error_log("Error eliminando asignación de materia: " . $e->getMessage());
            self::respondOrRedirect('error', 'Error al eliminar', 'Ocurrió un error inesperado al eliminar la asignación.', '/personal/asignacion-academica');
        }
    }

    public function toggleStatus()
    {
        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /personal/asignacion-academica");
            exit;
        }

        $id = $_POST['id_staff_materia'] ?? null;

        if (!ctype_digit((string) $id)) {
            self::respondOrRedirect('error', 'Asignación no válida', 'El identificador de la asignación no es válido.', '/personal/asignacion-academica');
        }

        $assignment = StaffMateria::findAssignment((int) $id);

        if (!$assignment) {
            self::respondOrRedirect('error', 'Asignación no encontrada', 'La asignación de materia solicitada no existe.', '/personal/asignacion-academica');
        }

        $currentState = $assignment['assignment_state'] === 'activo' ? 'activo' : 'inactivo';
        $newState = $currentState === 'activo' ? 'inactivo' : 'activo';

        try {
            StaffMateria::setState((int) $id, $newState);
            $title = $newState === 'activo' ? 'Asignación activada' : 'Asignación desactivada';
            $message = $newState === 'activo'
                ? 'La asignación de la materia fue reactivada correctamente.'
                : 'La asignación de la materia fue desactivada correctamente.';
            self::respondOrRedirect('success', $title, $message, '/personal/asignacion-academica');
        } catch (\PDOException $e) {
            error_log("Error cambiando estatus de asignación: " . $e->getMessage());
            self::respondOrRedirect('error', 'Error al cambiar estatus', 'Ocurrió un error inesperado al cambiar el estatus.', '/personal/asignacion-academica');
        }
    }

    public function exportPdf()
    {
        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        $cedula = Session::get('cedula');

        $filters = [
            'id_materia'  => $_GET['materia_filter'] ?? null,
            'id_trayecto' => $_GET['trayecto_filter'] ?? null,
            'state'       => $_GET['status_filter'] ?? null,
            'search'      => $_GET['search'] ?? null
        ];

        $assignments = StaffMateria::filter($filters);
        $totalRecords = count($assignments);
        $perPage = 50;
        $chunks = array_chunk($assignments, $perPage);

        $generatedBy = "C.I: " . htmlspecialchars($cedula ?? '---');
        $dateTime = date('d/m/Y - h:i A');

        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Reporte de Asignación Académica - UPTVal</title>
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
                    <span><strong>Total de asignaciones:</strong> ' . $totalRecords . '</span>
                </div>';
            }

            $html .= '<table>
                <thead>
                    <tr>
                        <th>Cedula</th>
                        <th>Docente</th>
                        <th>Materia</th>
                        <th>Trayecto</th>
                        <th>Duracion</th>
                        <th>Estatus</th>
                        <th>Fecha Asignacion</th>
                    </tr>
                </thead>
                <tbody>';

            foreach ($chunk as $assignment) {
                $assignmentDate = !empty($assignment['assignment_date'])
                    ? date('d/m/Y', strtotime($assignment['assignment_date']))
                    : '---';
                $state = $assignment['assignment_state'] === 'activo' ? 'Activo' : 'Inactivo';
                $html .= '<tr>
                    <td>' . htmlspecialchars($assignment['cedula']) . '</td>
                    <td>' . htmlspecialchars(trim(($assignment['last_name'] ?? '') . ', ' . ($assignment['first_name'] ?? ''))) . '</td>
                    <td>' . htmlspecialchars($assignment['materia_name']) . ' (' . htmlspecialchars($assignment['codigo']) . ')</td>
                    <td>' . htmlspecialchars($assignment['trayecto_name']) . '</td>
                    <td>' . htmlspecialchars($assignment['duracion']) . '</td>
                    <td>' . $state . '</td>
                    <td>' . $assignmentDate . '</td>
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

        $dompdf->stream('asignacion_academica_' . date('Ymd_His') . '.pdf', ['Attachment' => true]);
        exit;
    }

    private static function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    private static function respondOrRedirect($type, $title, $message, $url)
    {
        if (self::isAjax()) {
            header('Content-Type: application/json');
            echo json_encode([
                'ok'      => $type === 'success',
                'title'   => $title,
                'message' => $message
            ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
            exit;
        }

        $_SESSION['flash_message'] = [
            'type'    => $type,
            'title'   => $title,
            'message' => $message
        ];
        header("Location: " . $url);
        exit;
    }
}
