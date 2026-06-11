<?php

namespace Controllers;

class AuthController{

    public function showLogin(){
        $viewPath = '/var/www/html/app/View/login.php';

        if(file_exists($viewPath)){
            require_once $viewPath;
        }else{
            die('Error critico: La vista de Login no existe en la ruta esperada: ' . $viewPath);
        }
    }

    public function login(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            echo "<div style='background: #333; color: #fff; padding: 20px; font-family: monospace;'>";
            echo "<h3>Simulación de Recepción de Datos:</h3>";
            echo "<strong>Cédula capturada:</strong> " . htmlspecialchars($username) . "<br>";
            echo "<strong>Contraseña capturada:</strong> " . htmlspecialchars($password) . "<br>";
            echo "</div>";
        }else{
            echo "Acceso no autorizado";
        }
    }
}