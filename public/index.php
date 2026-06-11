<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Router;
$router = new Router();

$router->get('','AuthController', 'showLogin');
$router->get('login', 'AuthController', 'showLogin');
$router->post('login', 'AuthController', 'login');

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$router->resolve($uri, $method);

/*

echo "<h1>Probando Arquitectura MVC</h1>";

try{

    $conexion = \Core\Database::getInstance();
    echo "<p style='color: green;'>¡Éxito! Conexión a MySQL 9.0 establecida a través del Singleton y cargada con PSR-4.</p>";
}catch(Exception $e){

    echo "<p style='color: red;'>Fallo en la prueba: " . $e->getMessage() . "</p>";
}*/