<?php

namespace Controllers;

use Core\Session;
use Models\Staff;
use Models\Department;
use Models\TypeCondition;
use Models\ContractType;
use Dompdf\Dompdf;

class StaffController{

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
            'id_condition'  => $_GET['condition_filter'] ?? null,
            'id_contract'   => $_GET['contract_filter'] ?? null,
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

        // 4. Llamar al modelo con filtros
        $staffList = Staff::filter($filters, $limit, $offset);

        $departamentos = Department::all();
        $conditions = TypeCondition::all();
        $contractTypes = ContractType::all();
        $statsByType = Staff::getStatsByType($filters['id_department'], $filters['id_condition']);

        $userRoles = Session::get('user_roles') ?? [];

        require_once __DIR__ . '/../View/staff/index.php';
    }

    public function store() {

        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /personal");
            exit;
        }

        $data = [
            'cedula'         => trim($_POST['cedula'] ?? ''),
            'first_name'     => trim($_POST['first_name'] ?? ''),
            'last_name'      => trim($_POST['last_name'] ?? ''),
            'sex'            => $_POST['sex'] ?? '',
            'phone'          => trim($_POST['phone'] ?? ''),
            'email'          => trim($_POST['email'] ?? ''),
            'type_staff'     => $_POST['type_staff'] ?? '',
            'type_condition' => $_POST['type_condition'] ?? '',
            'id_department'  => $_POST['id_department'] ?? '',
            'pas'            => $_POST['pas'] ?? '',
            'type_contract'  => $_POST['type_contract'] ?? ''
        ];

        $errors = [];
        if ($data['cedula'] === '') {
            $errors[] = 'La cédula es obligatoria.';
        }
        if ($data['first_name'] === '') {
            $errors[] = 'El nombre es obligatorio.';
        }
        if ($data['last_name'] === '') {
            $errors[] = 'El apellido es obligatorio.';
        }
        if (!in_array($data['sex'], ['M', 'F'], true)) {
            $errors[] = 'Debe seleccionar el sexo.';
        }
        if ($data['email'] === '' || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Debe ingresar un correo válido.';
        }
        if (!in_array($data['type_staff'], ['Regular', 'Contratado'], true)) {
            $errors[] = 'Debe seleccionar el tipo de nombramiento.';
        }
        if (!in_array($data['pas'], ['Docente', 'Administrativo', 'Obrero'], true)) {
            $errors[] = 'Debe seleccionar el tipo de personal.';
        }
        if (!ctype_digit((string) $data['type_condition'])) {
            $errors[] = 'Debe seleccionar la condición.';
        }
        if (!ctype_digit((string) $data['id_department'])) {
            $errors[] = 'Debe seleccionar el departamento.';
        }
        if (!ctype_digit((string) $data['type_contract'])) {
            $errors[] = 'Debe seleccionar el tipo de contrato.';
        }

        if (!empty($errors)) {
            $_SESSION['flash_message'] = [
                'type'    => 'error',
                'title'   => 'Error de validación',
                'message' => implode(' ', $errors)
            ];
            header("Location: /personal");
            exit;
        }

        try {
            Staff::createWithUser($data);
            $_SESSION['flash_message'] = [
                'type'    => 'success',
                'title'   => 'Personal registrado',
                'message' => 'El personal se registró exitosamente en el sistema.'
            ];
        } catch (\PDOException $e) {
            if ($e->getCode() === '23000') {
                $_SESSION['flash_message'] = [
                    'type'    => 'error',
                    'title'   => 'Registro duplicado',
                    'message' => 'La cédula, el correo o el teléfono ya se encuentran registrados en el sistema.'
                ];
            } else {
                error_log("Error registrando personal: " . $e->getMessage());
                $_SESSION['flash_message'] = [
                    'type'    => 'error',
                    'title'   => 'Error al registrar',
                    'message' => 'Ocurrió un error inesperado al guardar el personal.'
                ];
            }
        }

        header("Location: /personal");
        exit;
    }

    public function exportPdfOne() {
        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        $id = $_GET['id'] ?? null;

        if (!ctype_digit((string) $id)) {
            $_SESSION['flash_message'] = [
                'type'    => 'error',
                'title'   => 'Registro no encontrado',
                'message' => 'El identificador del personal no es válido.'
            ];
            header("Location: /personal");
            exit;
        }

        $person = Staff::findById((int) $id);

        if (!$person) {
            $_SESSION['flash_message'] = [
                'type'    => 'error',
                'title'   => 'Registro no encontrado',
                'message' => 'El personal solicitado no existe en la base de datos.'
            ];
            header("Location: /personal");
            exit;
        }

        $cedula = Session::get('cedula');
        $generatedBy = "C.I: " . htmlspecialchars($cedula ?? '---');
        $dateTime = date('d/m/Y - h:i A');

        $sexLabel = $person['sex'] === 'M' ? 'Masculino' : 'Femenino';
        $condition = !empty($person['condition_name']) ? $person['condition_name'] : 'Sin condicion';
        $contract = !empty($person['contract_name']) ? $person['contract_name'] : 'Sin contrato';
        $department = !empty($person['department_name']) ? $person['department_name'] : 'Sin Asignar';
        $phone = !empty($person['phone']) ? $person['phone'] : 'No registrado';

        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Registro de Personal - UPTVal</title>
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
            <div class="title">Registro de Personal</div>
            <table class="data">
                <tr><td class="label">Cedula</td><td class="value">' . htmlspecialchars($person['cedula']) . '</td></tr>
                <tr><td class="label">Nombres</td><td class="value">' . htmlspecialchars($person['first_name']) . '</td></tr>
                <tr><td class="label">Apellidos</td><td class="value">' . htmlspecialchars($person['last_name']) . '</td></tr>
                <tr><td class="label">Sexo</td><td class="value">' . $sexLabel . '</td></tr>
                <tr><td class="label">Telefono</td><td class="value">' . htmlspecialchars($phone) . '</td></tr>
                <tr><td class="label">Correo Institucional</td><td class="value">' . htmlspecialchars($person['email']) . '</td></tr>
                <tr><td class="label">Tipo de Personal</td><td class="value">' . htmlspecialchars($person['pas']) . '</td></tr>
                <tr><td class="label">Tipo de Nombramiento</td><td class="value">' . htmlspecialchars($person['type_staff']) . '</td></tr>
                <tr><td class="label">Departamento</td><td class="value">' . htmlspecialchars($department) . '</td></tr>
                <tr><td class="label">Condicion</td><td class="value">' . htmlspecialchars($condition) . '</td></tr>
                <tr><td class="label">Tipo de Contrato</td><td class="value">' . htmlspecialchars($contract) . '</td></tr>
            </table>
            <div class="footer">Documento generado por el Sistema de Gestion Academica UPTVal.</div>
        </body>
        </html>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'registro_personal_' . preg_replace('/[^A-Za-z0-9]/', '', $person['cedula']) . '_' . date('Ymd_His') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
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
            'id_condition'  => $_GET['condition_filter'] ?? null,
            'id_contract'   => $_GET['contract_filter'] ?? null,
            'search'        => $_GET['search'] ?? null
        ];

        $staffList = Staff::filter($filters);
        $totalRecords = count($staffList);
        $perPage = 50;
        $chunks = array_chunk($staffList, $perPage);
        $totalPages = count($chunks);

        $generatedBy = "C.I: " . htmlspecialchars($cedula ?? '---');
        $dateTime = date('d/m/Y - h:i A');

        $html = '<!DOCTYPE html>
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
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Departamento</th>
                        <th>Condicion</th>
                        <th>Tipo Contrato</th>
                    </tr>
                </thead>
                <tbody>';

            foreach ($chunk as $person) {
                $conditionBadge = !empty($person['condition_name'])
                    ? htmlspecialchars($person['condition_name'])
                    : 'Sin condicion';
                $contractBadge = !empty($person['contract_name'])
                    ? htmlspecialchars($person['contract_name'])
                    : 'Sin contrato';
                $html .= '<tr>
                    <td>' . htmlspecialchars($person['cedula']) . '</td>
                    <td>' . htmlspecialchars($person['last_name'] . ', ' . $person['first_name']) . '</td>
                    <td>' . htmlspecialchars($person['pas']) . '</td>
                    <td>' . htmlspecialchars($person['department_name'] ?? 'Sin Asignar') . '</td>
                    <td>' . $conditionBadge . '</td>
                    <td>' . $contractBadge . '</td>
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

        $dompdf->stream('personal_' . date('Ymd_His') . '.pdf', ['Attachment' => true]);
        exit;
    }
}