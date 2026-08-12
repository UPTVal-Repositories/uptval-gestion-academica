<?php

namespace Controllers;

use Core\Session;
use Models\Materia;
use Models\Trayecto;
use Models\Especialidad;

class TrayectoController
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
            'status' => $_GET['status_filter'] ?? null,
            'search' => $_GET['search'] ?? null
        ];

        $limit = 10;
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
        $page = ($page < 1) ? 1 : $page;
        $offset = ($page - 1) * $limit;

        $totalRecords = Trayecto::countFilter($filters);
        $totalPages = ceil($totalRecords / $limit);

        if ($page > $totalPages && $totalPages > 0) {
            $page = $totalPages;
            $offset = ($page - 1) * $limit;
        }

        $trayectos = Trayecto::filter($filters, $limit, $offset);
        $materias = [];
        $especialidades = Especialidad::all();
        $duraciones = MateriaController::DURACIONES;
        $activeTab = 'trayectos';

        require_once __DIR__ . '/../View/materias/index.php';
    }

    public function store()
    {
        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /materias/trayectos");
            exit;
        }

        $name = trim($_POST['name'] ?? '');

        if ($name === '') {
            self::respondOrRedirect('error', 'Datos no válidos', 'Debe indicar el nombre del trayecto.', '/materias/trayectos');
        }

        if (Trayecto::findByName($name)) {
            self::respondOrRedirect('error', 'Trayecto duplicado', 'Ya existe un trayecto registrado con ese nombre.', '/materias/trayectos');
        }

        try {
            Trayecto::create($name);
            self::respondOrRedirect('success', 'Trayecto registrado', 'El trayecto se registró correctamente.', '/materias/trayectos');
        } catch (\PDOException $e) {
            error_log("Error registrando trayecto: " . $e->getMessage());
            self::respondOrRedirect('error', 'Error al registrar', 'Ocurrió un error inesperado al registrar el trayecto.', '/materias/trayectos');
        }
    }

    public function update()
    {
        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /materias/trayectos");
            exit;
        }

        $id = $_POST['id_trayecto'] ?? null;

        if (!ctype_digit((string) $id)) {
            self::respondOrRedirect('error', 'Trayecto no válido', 'El identificador del trayecto no es válido.', '/materias/trayectos');
        }

        $trayecto = Trayecto::findById((int) $id);
        if (!$trayecto) {
            self::respondOrRedirect('error', 'Trayecto no encontrado', 'El trayecto solicitado no existe en el sistema.', '/materias/trayectos');
        }

        $name = trim($_POST['name'] ?? '');

        if ($name === '') {
            self::respondOrRedirect('error', 'Datos no válidos', 'Debe indicar el nombre del trayecto.', '/materias/trayectos');
        }

        $existing = Trayecto::findByName($name);
        if ($existing && (int) $existing['id_trayecto'] !== (int) $id) {
            self::respondOrRedirect('error', 'Trayecto duplicado', 'Ya existe otro trayecto registrado con ese nombre.', '/materias/trayectos');
        }

        try {
            Trayecto::update((int) $id, $name);
            self::respondOrRedirect('success', 'Trayecto actualizado', 'El trayecto se actualizó correctamente.', '/materias/trayectos');
        } catch (\PDOException $e) {
            error_log("Error actualizando trayecto: " . $e->getMessage());
            self::respondOrRedirect('error', 'Error al actualizar', 'Ocurrió un error inesperado al actualizar el trayecto.', '/materias/trayectos');
        }
    }

    public function toggleStatus()
    {
        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /materias/trayectos");
            exit;
        }

        $id = $_POST['id_trayecto'] ?? null;

        if (!ctype_digit((string) $id)) {
            self::respondOrRedirect('error', 'Trayecto no válido', 'El identificador del trayecto no es válido.', '/materias/trayectos');
        }

        $trayecto = Trayecto::findById((int) $id);
        if (!$trayecto) {
            self::respondOrRedirect('error', 'Trayecto no encontrado', 'El trayecto solicitado no existe en el sistema.', '/materias/trayectos');
        }

        $newState = $trayecto['status'] === 'activo' ? 'inactivo' : 'activo';

        try {
            Trayecto::setState((int) $id, $newState);
            $title = $newState === 'activo' ? 'Trayecto activado' : 'Trayecto desactivado';
            $message = $newState === 'activo'
                ? 'El trayecto se reactivó correctamente.'
                : 'El trayecto se desactivó correctamente.';
            self::respondOrRedirect('success', $title, $message, '/materias/trayectos');
        } catch (\PDOException $e) {
            error_log("Error cambiando estatus de trayecto: " . $e->getMessage());
            self::respondOrRedirect('error', 'Error al cambiar estatus', 'Ocurrió un error inesperado al cambiar el estatus.', '/materias/trayectos');
        }
    }

    public function delete()
    {
        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /materias/trayectos");
            exit;
        }

        $id = $_POST['id_trayecto'] ?? null;

        if (!ctype_digit((string) $id)) {
            self::respondOrRedirect('error', 'Trayecto no válido', 'El identificador del trayecto no es válido.', '/materias/trayectos');
        }

        $trayecto = Trayecto::findById((int) $id);
        if (!$trayecto) {
            self::respondOrRedirect('error', 'Trayecto no encontrado', 'El trayecto solicitado no existe en el sistema.', '/materias/trayectos');
        }

        if (Trayecto::countMaterias((int) $id) > 0) {
            self::respondOrRedirect('error', 'Acción bloqueada', 'No se puede eliminar el trayecto porque tiene materias asociadas. Elimine o mueva las materias primero.', '/materias/trayectos');
        }

        try {
            Trayecto::delete((int) $id);
            self::respondOrRedirect('success', 'Trayecto eliminado', 'El trayecto fue eliminado correctamente.', '/materias/trayectos');
        } catch (\PDOException $e) {
            error_log("Error eliminando trayecto: " . $e->getMessage());
            self::respondOrRedirect('error', 'Error al eliminar', 'Ocurrió un error inesperado al eliminar el trayecto.', '/materias/trayectos');
        }
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
