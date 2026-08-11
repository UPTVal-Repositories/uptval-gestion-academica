<?php

namespace Controllers;

use Core\Session;
use Models\Rol;
use Models\RolUser;
use Models\Staff;
use Models\User;
use Dompdf\Dompdf;

class RoleController{

    public function index() {

        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        $cedula = Session::get('cedula');
        $last_connection_raw = Session::get('last_connection');
        $last_connection = !empty($last_connection_raw) ? date('d/m/Y - h:i a', strtotime($last_connection_raw)) : '';

        $filters = [
            'id_rol' => $_GET['role_filter'] ?? null,
            'state'  => $_GET['state_filter'] ?? null,
            'search' => $_GET['search'] ?? null
        ];

        $limit = 10;
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
        $page = ($page < 1) ? 1 : $page;
        $offset = ($page - 1) * $limit;

        $totalRecords = RolUser::countFilter($filters);
        $totalPages = ceil($totalRecords / $limit);

        if ($page > $totalPages && $totalPages > 0) {
            $page = $totalPages;
            $offset = ($page - 1) * $limit;
        }

        $roleAssignments = RolUser::filter($filters, $limit, $offset);
        $roles = Rol::all();

        $userRoles = Session::get('user_roles') ?? [];

        require_once __DIR__ . '/../View/roles/index.php';
    }

    public function searchByCedula() {

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

        $activeRoles = RolUser::getRolesByUserId((int) $staff['id_user']);

        echo json_encode([
            'ok'       => true,
            'staff'    => [
                'id_user'         => (int) $staff['id_user'],
                'cedula'          => $staff['cedula'],
                'first_name'      => $staff['first_name'],
                'last_name'       => $staff['last_name'],
                'department_name' => $staff['department_name'],
                'status'          => $staff['status']
            ],
            'roles'    => $activeRoles,
            'has_role' => count($activeRoles) > 0
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        exit;
    }

    public function store() {

        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /personal/permisos-roles");
            exit;
        }

        $idUser = $_POST['id_user'] ?? null;
        $idRol  = $_POST['id_rol'] ?? null;

        if (!ctype_digit((string) $idUser) || !ctype_digit((string) $idRol)) {
            $_SESSION['flash_message'] = [
                'type'    => 'error',
                'title'   => 'Datos no válidos',
                'message' => 'Debe seleccionar un usuario y un rol válidos.'
            ];
            header("Location: /personal/permisos-roles");
            exit;
        }

        $user = User::findById((int) $idUser);
        if (!$user) {
            $_SESSION['flash_message'] = [
                'type'    => 'error',
                'title'   => 'Usuario no encontrado',
                'message' => 'El usuario seleccionado no existe en el sistema.'
            ];
            header("Location: /personal/permisos-roles");
            exit;
        }

        if ($user['status'] !== 'activo' && $user['status'] !== 'pendiente') {
            $_SESSION['flash_message'] = [
                'type'    => 'error',
                'title'   => 'Usuario no asignable',
                'message' => 'El usuario debe estar en estado activo o pendiente para recibir un rol.'
            ];
            header("Location: /personal/permisos-roles");
            exit;
        }

        $rol = Rol::findById((int) $idRol);
        if (!$rol) {
            $_SESSION['flash_message'] = [
                'type'    => 'error',
                'title'   => 'Rol no encontrado',
                'message' => 'El rol seleccionado no existe en el sistema.'
            ];
            header("Location: /personal/permisos-roles");
            exit;
        }

        if ($rol['status'] !== 'activo') {
            $_SESSION['flash_message'] = [
                'type'    => 'error',
                'title'   => 'Rol inactivo',
                'message' => 'No se puede asignar un rol que se encuentra inactivo.'
            ];
            header("Location: /personal/permisos-roles");
            exit;
        }

        try {
            $result = RolUser::assign((int) $idUser, (int) $idRol);

            if ($result === 'assigned') {
                $_SESSION['flash_message'] = [
                    'type'    => 'success',
                    'title'   => 'Rol asignado',
                    'message' => 'El rol se asignó correctamente al usuario.'
                ];
            } elseif ($result === 'reactivated') {
                $_SESSION['flash_message'] = [
                    'type'    => 'success',
                    'title'   => 'Rol reactivado',
                    'message' => 'La asignación previa del rol fue reactivada.'
                ];
            } else {
                $_SESSION['flash_message'] = [
                    'type'    => 'error',
                    'title'   => 'Rol ya asignado',
                    'message' => 'El usuario ya cuenta con ese rol en estado activo.'
                ];
            }
        } catch (\PDOException $e) {
            error_log("Error asignando rol: " . $e->getMessage());
            $_SESSION['flash_message'] = [
                'type'    => 'error',
                'title'   => 'Error al asignar',
                'message' => 'Ocurrió un error inesperado al asignar el rol.'
            ];
        }

        header("Location: /personal/permisos-roles");
        exit;
    }

    public function deactivate() {

        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /personal/permisos-roles");
            exit;
        }

        $id = $_POST['id_rol_user'] ?? null;

        if (!ctype_digit((string) $id)) {
            $_SESSION['flash_message'] = [
                'type'    => 'error',
                'title'   => 'Asignación no válida',
                'message' => 'El identificador de la asignación no es válido.'
            ];
            header("Location: /personal/permisos-roles");
            exit;
        }

        $assignment = RolUser::findAssignment((int) $id);

        if (!$assignment) {
            $_SESSION['flash_message'] = [
                'type'    => 'error',
                'title'   => 'Asignación no encontrada',
                'message' => 'La asignación de rol solicitada no existe.'
            ];
            header("Location: /personal/permisos-roles");
            exit;
        }

        if ($assignment['assignment_state'] === 'historico') {
            $_SESSION['flash_message'] = [
                'type'    => 'error',
                'title'   => 'Rol ya desasignado',
                'message' => 'La asignación de este rol ya se encuentra en estado histórico.'
            ];
            header("Location: /personal/permisos-roles");
            exit;
        }

        if ($assignment['rol_name'] === 'Administrador' && RolUser::countActiveAdmins() <= 1) {
            $_SESSION['flash_message'] = [
                'type'    => 'error',
                'title'   => 'Acción bloqueada',
                'message' => 'No se puede quitar el rol al último administrador activo del sistema.'
            ];
            header("Location: /personal/permisos-roles");
            exit;
        }

        try {
            RolUser::deactivate((int) $id);
            $_SESSION['flash_message'] = [
                'type'    => 'success',
                'title'   => 'Rol desasignado',
                'message' => 'La asignación del rol pasó a estado histórico.'
            ];
        } catch (\PDOException $e) {
            error_log("Error desasignando rol: " . $e->getMessage());
            $_SESSION['flash_message'] = [
                'type'    => 'error',
                'title'   => 'Error al desasignar',
                'message' => 'Ocurrió un error inesperado al desasignar el rol.'
            ];
        }

        header("Location: /personal/permisos-roles");
        exit;
    }

    public function reactivate() {

        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /personal/permisos-roles");
            exit;
        }

        $id = $_POST['id_rol_user'] ?? null;

        if (!ctype_digit((string) $id)) {
            $_SESSION['flash_message'] = [
                'type'    => 'error',
                'title'   => 'Asignación no válida',
                'message' => 'El identificador de la asignación no es válido.'
            ];
            header("Location: /personal/permisos-roles");
            exit;
        }

        $assignment = RolUser::findAssignment((int) $id);

        if (!$assignment) {
            $_SESSION['flash_message'] = [
                'type'    => 'error',
                'title'   => 'Asignación no encontrada',
                'message' => 'La asignación de rol solicitada no existe.'
            ];
            header("Location: /personal/permisos-roles");
            exit;
        }

        if ($assignment['assignment_state'] === 'activo') {
            $_SESSION['flash_message'] = [
                'type'    => 'error',
                'title'   => 'Rol ya activo',
                'message' => 'La asignación de este rol ya se encuentra en estado activo.'
            ];
            header("Location: /personal/permisos-roles");
            exit;
        }

        try {
            RolUser::reactivate((int) $id);
            $_SESSION['flash_message'] = [
                'type'    => 'success',
                'title'   => 'Rol reactivado',
                'message' => 'La asignación del rol volvió a estado activo.'
            ];
        } catch (\PDOException $e) {
            error_log("Error reactivando rol: " . $e->getMessage());
            $_SESSION['flash_message'] = [
                'type'    => 'error',
                'title'   => 'Error al reactivar',
                'message' => 'Ocurrió un error inesperado al reactivar el rol.'
            ];
        }

        header("Location: /personal/permisos-roles");
        exit;
    }

    public function exportPdf() {
        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        $cedula = Session::get('cedula');

        $filters = [
            'id_rol' => $_GET['role_filter'] ?? null,
            'state'  => $_GET['state_filter'] ?? null,
            'search' => $_GET['search'] ?? null
        ];

        $assignments = RolUser::filter($filters);
        $totalRecords = count($assignments);
        $perPage = 50;
        $chunks = array_chunk($assignments, $perPage);

        $generatedBy = "C.I: " . htmlspecialchars($cedula ?? '---');
        $dateTime = date('d/m/Y - h:i A');

        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Reporte de Roles y Permisos - UPTVal</title>
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
                        <th>Nombre</th>
                        <th>Rol</th>
                        <th>Departamento</th>
                        <th>Estado</th>
                        <th>Fecha Asignacion</th>
                    </tr>
                </thead>
                <tbody>';

            foreach ($chunk as $assignment) {
                $state = $assignment['assignment_state'] === 'activo' ? 'Activo' : 'Historico';
                $assignmentDate = !empty($assignment['assignment_date'])
                    ? date('d/m/Y', strtotime($assignment['assignment_date']))
                    : '---';
                $html .= '<tr>
                    <td>' . htmlspecialchars($assignment['cedula']) . '</td>
                    <td>' . htmlspecialchars(trim(($assignment['last_name'] ?? '') . ', ' . ($assignment['first_name'] ?? ''))) . '</td>
                    <td>' . htmlspecialchars($assignment['rol_name']) . '</td>
                    <td>' . htmlspecialchars($assignment['department_name'] ?? 'Sin Asignar') . '</td>
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

        $dompdf->stream('roles_' . date('Ymd_His') . '.pdf', ['Attachment' => true]);
        exit;
    }
}
