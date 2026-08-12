<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Core\Router;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();
use Core\Session;
use Models\User;

Session::start();
date_default_timezone_set('America/Caracas');

// --- INTERCEPTO DE AUTO-LOGIN (Remember Me) ---
if (!Session::has('id_user') && isset($_COOKIE['remember_me'])) {
    $user = User::findByRememberToken($_COOKIE['remember_me']);
    if ($user && $user['status'] === 'activo') {
        Session::set('id_user', $user['id_user']);
        Session::set('cedula', $user['cedula']);
        header("Location: /dashboard");
        exit;
    }
}

$router = new Router();

$router->get('','AuthController', 'showLogin');
$router->get('login', 'AuthController', 'showLogin');
$router->post('login', 'AuthController', 'login');
// Rutas de Recuperación
$router->get('recuperar', 'AuthController', 'showForgotPassword');
$router->post('recuperar', 'AuthController', 'sendResetLink');
$router->get('restablecer', 'AuthController', 'showResetPassword');
$router->post('restablecer', 'AuthController', 'resetPassword');

$router->get('dashboard', 'DashboardController', 'index');
$router->get('documentacion', 'DocumentacionController', 'index');
$router->post('logout', 'AuthController', 'logout');

// --- RUTAS: Gestion de Departamentos ---
$router->get('departamentos', 'DepartmentController', 'index');
$router->get('departamentos/export-pdf', 'DepartmentController', 'exportPdf');
$router->get('departamentos/export-pdf-coordinator', 'DepartmentController', 'exportPdfCoordinator');
// --- RUTAS: Aulas y Laboratorios ---
$router->get('aulas', 'AulaController', 'index');
$router->post('aulas/store', 'AulaController', 'store');
$router->post('aulas/update', 'AulaController', 'update');
$router->post('aulas/toggle-status', 'AulaController', 'toggleStatus');
$router->post('aulas/delete', 'AulaController', 'delete');
$router->get('aulas/qr-svg', 'AulaController', 'qrSvg');
$router->get('aulas/export-qr-pdf', 'AulaController', 'exportQrPdf');

// --- RUTAS: Estudiantes (solo lectura) ---
$router->get('estudiantes', 'EstudianteController', 'index');
$router->get('estudiantes/export-pdf', 'EstudianteController', 'exportPdf');

// --- NUEVA RUTA: Gestión de Personal ---
$router->get('personal/export-pdf', 'StaffController', 'exportPdf');
$router->get('personal/export-pdf-one', 'StaffController', 'exportPdfOne');
$router->get('personal', 'StaffController', 'index');
$router->get('personal/permisos-roles', 'RoleController', 'index');
$router->post('personal/permisos-roles/store', 'RoleController', 'store');
$router->post('personal/permisos-roles/delete', 'RoleController', 'delete');
$router->post('personal/permisos-roles/toggle-status', 'RoleController', 'toggleStatus');
$router->get('personal/permisos-roles/search-by-cedula', 'RoleController', 'searchByCedula');
$router->get('personal/permisos-roles/export-pdf', 'RoleController', 'exportPdf');
$router->get('personal/asignacion-academica', 'AsignacionAcademicaController', 'index');
$router->get('personal/asignacion-academica/search-by-cedula', 'AsignacionAcademicaController', 'searchByCedula');
$router->get('personal/asignacion-academica/export-pdf', 'AsignacionAcademicaController', 'exportPdf');
$router->post('personal/asignacion-academica/store', 'AsignacionAcademicaController', 'store');
$router->post('personal/asignacion-academica/delete', 'AsignacionAcademicaController', 'delete');
$router->post('personal/asignacion-academica/toggle-status', 'AsignacionAcademicaController', 'toggleStatus');
$router->get('materias', 'MateriaController', 'index');
$router->post('materias/store', 'MateriaController', 'store');
$router->post('materias/update', 'MateriaController', 'update');
$router->post('materias/toggle-status', 'MateriaController', 'toggleStatus');
$router->post('materias/delete', 'MateriaController', 'delete');
$router->get('materias/trayectos', 'TrayectoController', 'index');
$router->post('materias/trayectos/store', 'TrayectoController', 'store');
$router->post('materias/trayectos/update', 'TrayectoController', 'update');
$router->post('materias/trayectos/toggle-status', 'TrayectoController', 'toggleStatus');
$router->post('materias/trayectos/delete', 'TrayectoController', 'delete');
$router->get('materias/especialidades', 'EspecialidadController', 'index');
$router->post('materias/especialidades/store', 'EspecialidadController', 'store');
$router->post('materias/especialidades/update', 'EspecialidadController', 'update');
$router->post('materias/especialidades/toggle-status', 'EspecialidadController', 'toggleStatus');
$router->post('materias/especialidades/delete', 'EspecialidadController', 'delete');
$router->post('personal/store', 'StaffController', 'store');
$router->post('personal/update', 'StaffController', 'update');

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$router->resolve($uri, $method);
