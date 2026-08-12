<?php

namespace Controllers;

use Core\Session;
use Models\Estudiante;
use Models\Trayecto;
use Models\Especialidad;

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
}
