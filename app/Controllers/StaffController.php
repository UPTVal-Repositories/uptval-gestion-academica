<?php

namespace Controllers;

use Core\Session;
use Models\Staff;
use Models\Department;

class StaffController{

/*
    public function index(){

        if(!Session::has('id_user')){
            header("Location: /login");
            exit;
        }

        $cedula = Session::get('cedula');

        $last_connection_raw = Session::get('last_connection');
        $last_connection = '';

        if (!empty($last_connection_raw)) {
            $last_connection = date('d/m/Y - h:i a', strtotime($last_connection_raw));
        }
        
        $staffList = Staff::all();
       
        if(!empty($staffList) && isset($staffList['id_staff'])){
            $staffList = [$staffList];
        }

        $limit = 10;
        // 2. Capturar la página actual desde la URL (ej. ?page=2). Si no existe, es la 1.
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
        if ($page < 1) {
            $page = 1;
        }

        $offset = ($page - 1) * $limit;

        $totalRecords = Staff::countAll();
        $totalPages = ceil($totalRecords / $limit); // Redondea hacia arriba

        // 5. Evitar que el usuario ponga una página que no existe en la URL
        if ($page > $totalPages && $totalPages > 0) {
            $page = $totalPages;
            $offset = ($page - 1) * $limit;
        }

        $staffList = Staff::paginate($limit, $offset);

        $departamentos = \Models\Department::all();

        /*echo "<pre style='background: #111; color: #0f0; padding: 20px; z-index: 9999; position: relative;'>";
            print_r($totalRecords);
        echo "</pre>";
        die();*//*

        require_once __DIR__ . '/../View/staff/index.php';
    }*/

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
            'status'        => $_GET['status_filter'] ?? null,
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

        // 4. Llamar al nuevo método filter que creamos en el modelo
        $staffList = Staff::filter($filters, $limit, $offset);

        $departamentos = \Models\Department::all();
        $statsByType = Staff::getStatsByType($filters['id_department']);

        require_once __DIR__ . '/../View/staff/index.php';
    }

    public function toggleStatus() {
        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        $idStaff = $_POST['id_staff'] ?? null;

        if ($idStaff && Staff::toggleStatus($idStaff)) {
            $newStatus = Staff::getStatus($idStaff);
            $msg = $newStatus === 'activo' ? 'Personal activado correctamente.' : 'Personal inactivado correctamente.';
            $_SESSION['flash_message'] = ['type' => 'success', 'title' => '¡Cambio de estado exitoso!', 'message' => $msg];
        }

        header("Location: /personal");
        exit;
    }
}