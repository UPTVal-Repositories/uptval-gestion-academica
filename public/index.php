<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Router;
use Core\Session;
use Models\User;

Session::start();

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


$router->get('dashboard', 'DashboardController', 'index');

$router->post('logout', 'AuthController', 'logout');



$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$router->resolve($uri, $method);
