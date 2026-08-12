<?php

namespace Controllers;

use Core\Session;
use Models\Aula;
use Models\Department;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\Output\QRMarkupHTML;
use chillerlan\QRCode\Output\QRMarkupSVG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Dompdf\Dompdf;

class AulaController
{
    const TIPOS = ['aula' => 'Aula', 'laboratorio' => 'Laboratorio'];

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
            'id_department' => $_GET['department_filter'] ?? null,
            'type'          => $_GET['type_filter'] ?? null,
            'status'        => $_GET['status_filter'] ?? null,
            'search'        => $_GET['search'] ?? null
        ];

        $limit = 10;
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
        $page = ($page < 1) ? 1 : $page;
        $offset = ($page - 1) * $limit;

        $totalRecords = Aula::countFilter($filters);
        $totalPages = ceil($totalRecords / $limit);

        if ($page > $totalPages && $totalPages > 0) {
            $page = $totalPages;
            $offset = ($page - 1) * $limit;
        }

        $aulas = Aula::filter($filters, $limit, $offset);
        $departamentos = Department::all();
        $tipos = self::TIPOS;

        require_once __DIR__ . '/../View/aulas/index.php';
    }

    public function store()
    {
        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /aulas");
            exit;
        }

        $code         = trim($_POST['code'] ?? '');
        $name         = trim($_POST['name'] ?? '');
        $type         = $_POST['type'] ?? '';
        $idDepartment = $_POST['id_department'] ?? null;

        if ($code === '' || $name === '' || !ctype_digit((string) $idDepartment)) {
            self::respondOrRedirect('error', 'Datos no validos', 'Debe completar el codigo, nombre y departamento.', '/aulas');
        }

        if (!array_key_exists($type, self::TIPOS)) {
            self::respondOrRedirect('error', 'Tipo no valido', 'Seleccione un tipo valido (Aula o Laboratorio).', '/aulas');
        }

        if (!Department::findById((int) $idDepartment)) {
            self::respondOrRedirect('error', 'Departamento no encontrado', 'El departamento seleccionado no existe.', '/aulas');
        }

        if (Aula::findByCode($code)) {
            self::respondOrRedirect('error', 'Codigo duplicado', 'Ya existe un aula registrada con ese codigo.', '/aulas');
        }

        try {
            Aula::create([
                'code'          => $code,
                'name'          => $name,
                'type'          => $type,
                'id_department' => (int) $idDepartment
            ]);
            self::respondOrRedirect('success', 'Aula registrada', 'El aula se registro correctamente.', '/aulas');
        } catch (\PDOException $e) {
            error_log("Error registrando aula: " . $e->getMessage());
            self::respondOrRedirect('error', 'Error al registrar', 'Ocurrio un error inesperado al registrar el aula.', '/aulas');
        }
    }

    public function update()
    {
        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /aulas");
            exit;
        }

        $id = $_POST['id_aula'] ?? null;

        if (!ctype_digit((string) $id)) {
            header("Location: /aulas");
            exit;
        }

        $aula = Aula::findById((int) $id);
        if (!$aula) {
            header("Location: /aulas");
            exit;
        }

        $code         = trim($_POST['code'] ?? '');
        $name         = trim($_POST['name'] ?? '');
        $type         = $_POST['type'] ?? '';
        $idDepartment = $_POST['id_department'] ?? null;

        if ($code === '' || $name === '' || !ctype_digit((string) $idDepartment)) {
            self::respondOrRedirect('error', 'Datos no validos', 'Debe completar el codigo, nombre y departamento.', '/aulas');
        }

        if (!array_key_exists($type, self::TIPOS)) {
            self::respondOrRedirect('error', 'Tipo no valido', 'Seleccione un tipo valido (Aula o Laboratorio).', '/aulas');
        }

        if (!Department::findById((int) $idDepartment)) {
            self::respondOrRedirect('error', 'Departamento no encontrado', 'El departamento seleccionado no existe.', '/aulas');
        }

        $existing = Aula::findByCode($code);
        if ($existing && (int) $existing['id_aula'] !== (int) $id) {
            self::respondOrRedirect('error', 'Codigo duplicado', 'Ya existe otra aula registrada con ese codigo.', '/aulas');
        }

        try {
            Aula::update((int) $id, [
                'code'          => $code,
                'name'          => $name,
                'type'          => $type,
                'id_department' => (int) $idDepartment
            ]);
            self::respondOrRedirect('success', 'Aula actualizada', 'El aula se actualizo correctamente.', '/aulas');
        } catch (\PDOException $e) {
            error_log("Error actualizando aula: " . $e->getMessage());
            self::respondOrRedirect('error', 'Error al actualizar', 'Ocurrio un error inesperado al actualizar el aula.', '/aulas');
        }
    }

    public function toggleStatus()
    {
        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /aulas");
            exit;
        }

        $id = $_POST['id_aula'] ?? null;

        if (!ctype_digit((string) $id)) {
            self::respondOrRedirect('error', 'Aula no valida', 'El identificador del aula no es valido.', '/aulas');
        }

        $aula = Aula::findById((int) $id);
        if (!$aula) {
            self::respondOrRedirect('error', 'Aula no encontrada', 'El aula solicitada no existe.', '/aulas');
        }

        $newState = $aula['status'] === 'activo' ? 'inactivo' : 'activo';

        try {
            Aula::setState((int) $id, $newState);
            $title = $newState === 'activo' ? 'Aula activada' : 'Aula desactivada';
            $message = $newState === 'activo'
                ? 'El aula se reactivo correctamente.'
                : 'El aula se desactivo correctamente.';
            self::respondOrRedirect('success', $title, $message, '/aulas');
        } catch (\PDOException $e) {
            error_log("Error cambiando estatus de aula: " . $e->getMessage());
            self::respondOrRedirect('error', 'Error al cambiar estatus', 'Ocurrio un error inesperado al cambiar el estatus.', '/aulas');
        }
    }

    public function delete()
    {
        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /aulas");
            exit;
        }

        $id = $_POST['id_aula'] ?? null;

        if (!ctype_digit((string) $id)) {
            self::respondOrRedirect('error', 'Aula no valida', 'El identificador del aula no es valido.', '/aulas');
        }

        $aula = Aula::findById((int) $id);
        if (!$aula) {
            self::respondOrRedirect('error', 'Aula no encontrada', 'El aula solicitada no existe.', '/aulas');
        }

        try {
            Aula::delete((int) $id);
            self::respondOrRedirect('success', 'Aula eliminada', 'El aula fue eliminada correctamente.', '/aulas');
        } catch (\PDOException $e) {
            error_log("Error eliminando aula: " . $e->getMessage());
            self::respondOrRedirect('error', 'Error al eliminar', 'Ocurrio un error inesperado al eliminar el aula.', '/aulas');
        }
    }

    public function qrSvg()
    {
        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        $id = $_GET['id_aula'] ?? null;

        if (!ctype_digit((string) $id)) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'message' => 'Aula no valida']);
            exit;
        }

        $aula = Aula::findById((int) $id);
        if (!$aula) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'message' => 'Aula no encontrada']);
            exit;
        }

        $qrData = self::buildQrData($aula);

        $options = new QROptions([
            'outputInterface' => QRMarkupSVG::class,
            'eccLevel'        => EccLevel::M,
            'outputBase64'    => false,
            'svgAddXmlHeader' => false,
        ]);

        $qrcode = new QRCode($options);
        $svg = $qrcode->render($qrData);

        header('Content-Type: image/svg+xml');
        echo $svg;
        exit;
    }

    public function exportQrPdf()
    {
        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        $id = $_GET['id_aula'] ?? null;

        if (!ctype_digit((string) $id)) {
            header("Location: /aulas");
            exit;
        }

        $aula = Aula::findById((int) $id);
        if (!$aula) {
            header("Location: /aulas");
            exit;
        }

        $qrData = self::buildQrData($aula);

        if (extension_loaded('gd')) {
            $options = new QROptions([
                'outputInterface' => QRGdImagePNG::class,
                'outputBase64'    => true,
            ]);
        } else {
            $options = new QROptions([
                'outputInterface' => QRMarkupSVG::class,
                'outputBase64'    => true,
                'svgAddXmlHeader' => false,
            ]);
        }

        $qrcode = new QRCode($options);
        $qrBase64 = $qrcode->render($qrData);

        $tipoLabel = self::TIPOS[$aula['type']] ?? 'Aula';
        $departamento = htmlspecialchars($aula['department_name']);
        $codigo = htmlspecialchars($aula['code']);

        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>QR ' . $tipoLabel . ' ' . $codigo . ' - UPTVal</title>
            <style>
                body { font-family: DejaVu Sans, sans-serif; margin: 0; padding: 20px; text-align: center; }
                .header { border-bottom: 2px solid #d97b29; padding-bottom: 12px; margin-bottom: 20px; }
                .header h1 { margin: 0; font-size: 18pt; color: #0f172a; }
                .header h1 span { color: #d97b29; }
                .header p { margin: 4px 0 0; font-size: 9pt; color: #64748b; }
                .title { font-size: 12pt; font-weight: bold; color: #0f172a; margin: 16px 0 4px; }
                .subtitle { font-size: 9pt; color: #475569; margin-bottom: 20px; }
                .qr-container { display: inline-block; border: 2px solid #e2e8f0; padding: 16px; border-radius: 12px; }
                .qr-container img { width: 220px; height: 220px; }
                .footer { margin-top: 24px; font-size: 9pt; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 12px; }
                .footer strong { color: #0f172a; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1><span>UPT</span>Val</h1>
                <p>Universidad Politecnica Territorial de Valencia</p>
            </div>
            <div class="title">' . $tipoLabel . ' ' . $codigo . '</div>
            <div class="subtitle">Departamento de ' . $departamento . ' &middot; Escanee este codigo para registrar asistencia</div>
            <div class="qr-container">
                <img src="' . $qrBase64 . '" alt="QR ' . $codigo . '">
            </div>
            <div class="footer">
                ' . $tipoLabel . ' <strong>' . $codigo . '</strong> &middot; ' . $departamento . ' &middot; UPTVal Sistema de Gestion Academica
            </div>
        </body>
        </html>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'qr_' . strtolower(preg_replace('/[^A-Za-z0-9]/', '_', $codigo)) . '_' . date('Ymd_His') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }

    private static function buildQrData($aula)
    {
        return json_encode([
            'type'       => 'UPTVAL_AULA',
            'code'       => $aula['code'],
            'id_aula'    => (int) $aula['id_aula'],
            'department' => $aula['department_name']
        ]);
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
