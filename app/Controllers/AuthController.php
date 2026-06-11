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
         
            $cedula = $_POST['cedula'] ?? '';
            $password = $_POST['password'] ?? '';

            $error_message = '';
            $success_message = '';

            //buscamos el usuario en la base de datos.
            $user = User::findByCedula($cedula);

            if($user){

                if(password_verify($password, $user['password'])){

                    if($user['status'] === 'activo'){

                        /*echo "<div style='background: #198754; color: #fff; padding: 20px;'>";
                        echo "<h3>¡Acceso Autorizado!</h3>";
                        echo "<p>Bienvenido al sistema. Tu ID de usuario es: " . $user['id_user'] . "</p>";
                        echo "</div>"; */
                        $success_message = "¡Autenticación exitosa! Bienvenido.";
                    }elseif($user['status'] === 'pendiente'){
                       //echo "<h3>Cuenta no verificada. Por favor, revise su correo.</h3>";
                       $error_message = "Cuenta no verificada. Por favor, revise su correo.";
                    }else{
                        //echo "<h3>Acceso denegado. Estado de cuenta: " . htmlspecialchars($user['status']) . "</h3>";
                        $error_message = "Acceso denegado. Estado: " . htmlspecialchars($user['status']);
                    }
                }else{
                    //echo "<p>La clave es incorrecta</p>";
                    $error_message = "La clave es incorrecta.";
                }
            }else{
                //echo "<p>El usuario no existe en la base de datos</p>";
                $error_message = "No existe un usuario con esta cédula.";
            }

            $viewPath = '/var/www/html/app/View/login.php';

            if(file_exists($viewPath)){
                require_once $viewPath;
            
            }else{
                die("Error: No se encontró la vista.");
            }

        }else{
            header("Location: /");
            exit;
        }
    }
}