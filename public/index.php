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
$router->post('logout', 'AuthController', 'logout');

// --- NUEVA RUTA: Gestión de Personal ---
$router->get('personal/export-pdf', 'StaffController', 'exportPdf');
$router->get('personal/export-pdf-one', 'StaffController', 'exportPdfOne');
$router->get('personal', 'StaffController', 'index');
$router->post('personal/store', 'StaffController', 'store');
$router->post('personal/update', 'StaffController', 'update');

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$router->resolve($uri, $method);
