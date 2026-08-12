<?php

namespace Controllers;

use Core\Session;
use Models\Staff;
use Models\Estudiante;

class DashboardController {

    public function index() {
        
        // 1. Capa de Seguridad: Verificamos si el usuario NO está logueado
        if (!Session::has('id_user')) {
            // Si no hay sesión, lo devolvemos al login de inmediato
            header("Location: /");
            exit;
        }

        // 2. Si pasa la validación, extraemos sus datos de la sesión
        $idUser = Session::get('id_user');
        $cedula = Session::get('cedula');

        $last_connection_raw = Session::get('last_connection');
        $last_connection = '';

        $staffStats = Staff::getDashboardStats();
        $statsByType = Staff::getStatsByType();

        
        $total_staff     = $staffStats['total'] ?? 0;
        $activos_staff   = $staffStats['activos'] ?? 0;
        $inactivos_staff = $staffStats['inactivos'] ?? 0;

        $estudianteStats = Estudiante::getDashboardStats();
        $total_estudiantes     = $estudianteStats['total'] ?? 0;
        $activos_estudiantes   = $estudianteStats['activos'] ?? 0;
        $inactivos_estudiantes = $estudianteStats['inactivos'] ?? 0;

        $userRoles = Session::get('user_roles') ?? [];


        if (!empty($last_connection_raw)) {
            $last_connection = date('d/m/Y - h:i a', strtotime($last_connection_raw));
        }

        $viewPath = __DIR__ . '/../View/dashboard/index.php';
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("Error: No se encontró la vista en la ruta calculada: " . $viewPath);
        }
    }
}