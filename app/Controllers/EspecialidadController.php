<?php

namespace Controllers;

use Core\Session;
use Models\Especialidad;
use Models\Materia;
use Models\Trayecto;

class EspecialidadController
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

        $totalRecords = Especialidad::countFilter($filters);
        $totalPages = ceil($totalRecords / $limit);

        if ($page > $totalPages && $totalPages > 0) {
            $page = $totalPages;
            $offset = ($page - 1) * $limit;
        }

        $especialidades = Especialidad::filter($filters, $limit, $offset);
        $trayectos = Trayecto::all();
        $materias = [];
        $duraciones = MateriaController::DURACIONES;
        $activeTab = 'especialidades';

        require_once __DIR__ . '/../View/materias/index.php';
    }

    public function store()
    {
        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /materias/especialidades");
            exit;
        }

        $name = trim($_POST['name'] ?? '');

        if ($name === '') {
            self::respondOrRedirect('error', 'Datos no validos', 'Debe indicar el nombre de la especialidad.', '/materias/especialidades');
        }

        if (Especialidad::findByName($name)) {
            self::respondOrRedirect('error', 'Especialidad duplicada', 'Ya existe una especialidad registrada con ese nombre.', '/materias/especialidades');
        }

        try {
            Especialidad::create($name);
            self::respondOrRedirect('success', 'Especialidad registrada', 'La especialidad se registro correctamente.', '/materias/especialidades');
        } catch (\PDOException $e) {
            error_log("Error registrando especialidad: " . $e->getMessage());
            self::respondOrRedirect('error', 'Error al registrar', 'Ocurrio un error inesperado al registrar la especialidad.', '/materias/especialidades');
        }
    }

    public function update()
    {
        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /materias/especialidades");
            exit;
        }

        $id = $_POST['id_especialidad'] ?? null;

        if (!ctype_digit((string) $id)) {
            self::respondOrRedirect('error', 'Especialidad no valida', 'El identificador de la especialidad no es valido.', '/materias/especialidades');
        }

        $especialidad = Especialidad::findById((int) $id);
        if (!$especialidad) {
            self::respondOrRedirect('error', 'Especialidad no encontrada', 'La especialidad solicitada no existe en el sistema.', '/materias/especialidades');
        }

        $name = trim($_POST['name'] ?? '');

        if ($name === '') {
            self::respondOrRedirect('error', 'Datos no validos', 'Debe indicar el nombre de la especialidad.', '/materias/especialidades');
        }

        $existing = Especialidad::findByName($name);
        if ($existing && (int) $existing['id_especialidad'] !== (int) $id) {
            self::respondOrRedirect('error', 'Especialidad duplicada', 'Ya existe otra especialidad registrada con ese nombre.', '/materias/especialidades');
        }

        try {
            Especialidad::update((int) $id, $name);
            self::respondOrRedirect('success', 'Especialidad actualizada', 'La especialidad se actualizo correctamente.', '/materias/especialidades');
        } catch (\PDOException $e) {
            error_log("Error actualizando especialidad: " . $e->getMessage());
            self::respondOrRedirect('error', 'Error al actualizar', 'Ocurrio un error inesperado al actualizar la especialidad.', '/materias/especialidades');
        }
    }

    public function toggleStatus()
    {
        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /materias/especialidades");
            exit;
        }

        $id = $_POST['id_especialidad'] ?? null;

        if (!ctype_digit((string) $id)) {
            self::respondOrRedirect('error', 'Especialidad no valida', 'El identificador de la especialidad no es valido.', '/materias/especialidades');
        }

        $especialidad = Especialidad::findById((int) $id);
        if (!$especialidad) {
            self::respondOrRedirect('error', 'Especialidad no encontrada', 'La especialidad solicitada no existe en el sistema.', '/materias/especialidades');
        }

        $newState = $especialidad['status'] === 'activo' ? 'inactivo' : 'activo';

        try {
            Especialidad::setState((int) $id, $newState);
            $title = $newState === 'activo' ? 'Especialidad activada' : 'Especialidad desactivada';
            $message = $newState === 'activo'
                ? 'La especialidad se reactivo correctamente.'
                : 'La especialidad se desactivo correctamente.';
            self::respondOrRedirect('success', $title, $message, '/materias/especialidades');
        } catch (\PDOException $e) {
            error_log("Error cambiando estatus de especialidad: " . $e->getMessage());
            self::respondOrRedirect('error', 'Error al cambiar estatus', 'Ocurrio un error inesperado al cambiar el estatus.', '/materias/especialidades');
        }
    }

    public function delete()
    {
        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /materias/especialidades");
            exit;
        }

        $id = $_POST['id_especialidad'] ?? null;

        if (!ctype_digit((string) $id)) {
            self::respondOrRedirect('error', 'Especialidad no valida', 'El identificador de la especialidad no es valido.', '/materias/especialidades');
        }

        $especialidad = Especialidad::findById((int) $id);
        if (!$especialidad) {
            self::respondOrRedirect('error', 'Especialidad no encontrada', 'La especialidad solicitada no existe en el sistema.', '/materias/especialidades');
        }

        if (Especialidad::countMaterias((int) $id) > 0) {
            self::respondOrRedirect('error', 'Accion bloqueada', 'No se puede eliminar la especialidad porque tiene materias asociadas. Elimine o mueva las materias primero.', '/materias/especialidades');
        }

        try {
            Especialidad::delete((int) $id);
            self::respondOrRedirect('success', 'Especialidad eliminada', 'La especialidad fue eliminada correctamente.', '/materias/especialidades');
        } catch (\PDOException $e) {
            error_log("Error eliminando especialidad: " . $e->getMessage());
            self::respondOrRedirect('error', 'Error al eliminar', 'Ocurrio un error inesperado al eliminar la especialidad.', '/materias/especialidades');
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
