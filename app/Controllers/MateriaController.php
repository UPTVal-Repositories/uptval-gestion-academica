<?php

namespace Controllers;

use Core\Session;
use Models\Materia;
use Models\Trayecto;
use Models\Especialidad;

class MateriaController
{
    const DURACIONES = ['12 semanas', '18 semanas', '24 semanas'];

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
            'duracion'        => $_GET['duracion_filter'] ?? null,
            'status'          => $_GET['status_filter'] ?? null,
            'search'          => $_GET['search'] ?? null
        ];

        $limit = 10;
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
        $page = ($page < 1) ? 1 : $page;
        $offset = ($page - 1) * $limit;

        $totalRecords = Materia::countFilter($filters);
        $totalPages = ceil($totalRecords / $limit);

        if ($page > $totalPages && $totalPages > 0) {
            $page = $totalPages;
            $offset = ($page - 1) * $limit;
        }

        $materias = Materia::filter($filters, $limit, $offset);
        $trayectos = Trayecto::all();
        $especialidades = Especialidad::all();
        $duraciones = self::DURACIONES;
        $activeTab = 'materias';

        require_once __DIR__ . '/../View/materias/index.php';
    }

    public function store()
    {
        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /materias");
            exit;
        }

        $codigo         = trim($_POST['codigo'] ?? '');
        $name           = trim($_POST['name'] ?? '');
        $duracion       = $_POST['duracion'] ?? '';
        $idTrayecto     = $_POST['id_trayecto'] ?? null;
        $idEspecialidad = $_POST['id_especialidad'] ?? null;

        if ($codigo === '' || $name === '' || !ctype_digit((string) $idTrayecto)) {
            self::respondOrRedirect('error', 'Datos no validos', 'Debe completar el codigo, el nombre y el trayecto de la materia.', '/materias');
        }

        if (!in_array($duracion, self::DURACIONES, true)) {
            self::respondOrRedirect('error', 'Duracion no valida', 'Seleccione una duracion valida para la materia.', '/materias');
        }

        if (!Trayecto::findById((int) $idTrayecto)) {
            self::respondOrRedirect('error', 'Trayecto no encontrado', 'El trayecto seleccionado no existe en el sistema.', '/materias');
        }

        if (!empty($idEspecialidad) && ctype_digit((string) $idEspecialidad) && !Especialidad::findById((int) $idEspecialidad)) {
            self::respondOrRedirect('error', 'Especialidad no encontrada', 'La especialidad seleccionada no existe en el sistema.', '/materias');
        }

        if (Materia::findByCodigo($codigo)) {
            self::respondOrRedirect('error', 'Codigo duplicado', 'Ya existe una materia registrada con ese codigo.', '/materias');
        }

        try {
            Materia::create([
                'codigo'          => $codigo,
                'name'            => $name,
                'duracion'        => $duracion,
                'id_trayecto'     => (int) $idTrayecto,
                'id_especialidad' => !empty($idEspecialidad) && ctype_digit((string) $idEspecialidad) ? (int) $idEspecialidad : null
            ]);
            self::respondOrRedirect('success', 'Materia registrada', 'La materia se registro correctamente en el sistema.', '/materias');
        } catch (\PDOException $e) {
            error_log("Error registrando materia: " . $e->getMessage());
            self::respondOrRedirect('error', 'Error al registrar', 'Ocurrió un error inesperado al registrar la materia.', '/materias');
        }
    }

    public function update()
    {
        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /materias");
            exit;
        }

        $id = $_POST['id_materia'] ?? null;

        if (!ctype_digit((string) $id)) {
            self::respondOrRedirect('error', 'Materia no válida', 'El identificador de la materia no es válido.', '/materias');
        }

        $materia = Materia::findById((int) $id);
        if (!$materia) {
            self::respondOrRedirect('error', 'Materia no encontrada', 'La materia solicitada no existe en el sistema.', '/materias');
        }

        $codigo         = trim($_POST['codigo'] ?? '');
        $name           = trim($_POST['name'] ?? '');
        $duracion       = $_POST['duracion'] ?? '';
        $idTrayecto     = $_POST['id_trayecto'] ?? null;
        $idEspecialidad = $_POST['id_especialidad'] ?? null;

        if ($codigo === '' || $name === '' || !ctype_digit((string) $idTrayecto)) {
            self::respondOrRedirect('error', 'Datos no validos', 'Debe completar el codigo, el nombre y el trayecto de la materia.', '/materias');
        }

        if (!in_array($duracion, self::DURACIONES, true)) {
            self::respondOrRedirect('error', 'Duracion no valida', 'Seleccione una duracion valida para la materia.', '/materias');
        }

        if (!Trayecto::findById((int) $idTrayecto)) {
            self::respondOrRedirect('error', 'Trayecto no encontrado', 'El trayecto seleccionado no existe en el sistema.', '/materias');
        }

        $existing = Materia::findByCodigo($codigo);
        if ($existing && (int) $existing['id_materia'] !== (int) $id) {
            self::respondOrRedirect('error', 'Codigo duplicado', 'Ya existe otra materia registrada con ese codigo.', '/materias');
        }

        try {
            Materia::update((int) $id, [
                'codigo'          => $codigo,
                'name'            => $name,
                'duracion'        => $duracion,
                'id_trayecto'     => (int) $idTrayecto,
                'id_especialidad' => !empty($idEspecialidad) && ctype_digit((string) $idEspecialidad) ? (int) $idEspecialidad : null
            ]);
            self::respondOrRedirect('success', 'Materia actualizada', 'La materia se actualizó correctamente.', '/materias');
        } catch (\PDOException $e) {
            error_log("Error actualizando materia: " . $e->getMessage());
            self::respondOrRedirect('error', 'Error al actualizar', 'Ocurrió un error inesperado al actualizar la materia.', '/materias');
        }
    }

    public function toggleStatus()
    {
        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /materias");
            exit;
        }

        $id = $_POST['id_materia'] ?? null;

        if (!ctype_digit((string) $id)) {
            self::respondOrRedirect('error', 'Materia no válida', 'El identificador de la materia no es válido.', '/materias');
        }

        $materia = Materia::findById((int) $id);
        if (!$materia) {
            self::respondOrRedirect('error', 'Materia no encontrada', 'La materia solicitada no existe en el sistema.', '/materias');
        }

        $newState = $materia['status'] === 'activo' ? 'inactivo' : 'activo';

        try {
            Materia::setState((int) $id, $newState);
            $title = $newState === 'activo' ? 'Materia activada' : 'Materia desactivada';
            $message = $newState === 'activo'
                ? 'La materia se reactivó correctamente.'
                : 'La materia se desactivó correctamente.';
            self::respondOrRedirect('success', $title, $message, '/materias');
        } catch (\PDOException $e) {
            error_log("Error cambiando estatus de materia: " . $e->getMessage());
            self::respondOrRedirect('error', 'Error al cambiar estatus', 'Ocurrió un error inesperado al cambiar el estatus.', '/materias');
        }
    }

    public function delete()
    {
        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /materias");
            exit;
        }

        $id = $_POST['id_materia'] ?? null;

        if (!ctype_digit((string) $id)) {
            self::respondOrRedirect('error', 'Materia no válida', 'El identificador de la materia no es válido.', '/materias');
        }

        $materia = Materia::findById((int) $id);
        if (!$materia) {
            self::respondOrRedirect('error', 'Materia no encontrada', 'La materia solicitada no existe en el sistema.', '/materias');
        }

        if (Materia::countDocentes((int) $id) > 0) {
            self::respondOrRedirect('error', 'Acción bloqueada', 'No se puede eliminar la materia porque tiene docentes asignados. Desasigne las materias primero.', '/materias');
        }

        try {
            Materia::delete((int) $id);
            self::respondOrRedirect('success', 'Materia eliminada', 'La materia fue eliminada correctamente.', '/materias');
        } catch (\PDOException $e) {
            error_log("Error eliminando materia: " . $e->getMessage());
            self::respondOrRedirect('error', 'Error al eliminar', 'Ocurrió un error inesperado al eliminar la materia.', '/materias');
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
