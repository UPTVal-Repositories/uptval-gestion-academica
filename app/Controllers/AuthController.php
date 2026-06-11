<?php

namespace Controllers;

use Models\User;

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
            /*$username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            echo "<div style='background: #333; color: #fff; padding: 20px; font-family: monospace;'>";
            echo "<h3>Simulación de Recepción de Datos:</h3>";
            echo "<strong>Cédula capturada:</strong> " . htmlspecialchars($username) . "<br>";
            echo "<strong>Contraseña capturada:</strong> " . htmlspecialchars($password) . "<br>";
            echo "</div>";*/

            $cedula = $_POST['cedula'] ?? '';
            $password = $_POST['password'] ?? '';

            //buscamos el usuario en la base de datos.
            $user = User::findByCedula($cedula);

            if($user){

                if(password_verify($password, $user['password'])){

                    if($user['status'] === 'activo'){

                        echo "<div style='background: #198754; color: #fff; padding: 20px;'>";
                        echo "<h3>¡Acceso Autorizado!</h3>";
                        echo "<p>Bienvenido al sistema. Tu ID de usuario es: " . $user['id_user'] . "</p>";
                        echo "</div>"; 
                    }elseif($user['status'] === 'pendiente'){
                       echo "<h3>Cuenta no verificada. Por favor, revise su correo.</h3>";
                    }else{
                        echo "<h3>Acceso denegado. Estado de cuenta: " . htmlspecialchars($user['status']) . "</h3>";
                    }
                }else{
                    echo "<p>La clave es incorrecta</p>";
                }
            }else{
                echo "<p>El usuario no existe en la base de datos</p>";
            }

        }else{
            echo "Acceso no autorizado";
        }
    }
}