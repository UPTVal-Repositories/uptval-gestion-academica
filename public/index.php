<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Router;
use Core\Session;

Session::start();

$router = new Router();

$router->get('','AuthController', 'showLogin');
$router->get('login', 'AuthController', 'showLogin');
$router->post('login', 'AuthController', 'login');

$router->get('dashboard', 'DashboardController', 'index');


$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$router->resolve($uri, $method);

