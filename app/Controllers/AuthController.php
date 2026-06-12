<?php

namespace Controllers;

use Models\User;
use Core\Session;

class AuthController{

    public function showLogin(){
        if (Session::has('id_user')) {
            header("Location: /dashboard");
            exit;
        }

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


                        Session::start();
                        Session::set('id_user', $user['id_user']);
                        Session::set('cedula', $user['cedula']);
                        
                        // Guardamos la fecha de su sesión anterior y actualizamos a la actual
                        Session::set('last_connection', $user['last_connection']);
                        User::updateLastConnection($user['id_user']);

                        // Lógica de "Recordarme"
                        if (isset($_POST['remember'])) {
                            $token = bin2hex(random_bytes(32)); // 64 caracteres
                            error_log("Token generado para usuario " . $user['id_user'] . ": " . $token); // DEBUG
                            User::updateRememberToken($user['id_user'], $token);
                            
                            // Cookie válida por 30 días, HttpOnly y Secure (si aplica)
                            setcookie('remember_me', $token, time() + (30 * 24 * 60 * 60), "/", "", false, true);
                        }

                        //redirigir el flujo al dashboard.
                        header("Location: /dashboard");
                        exit;

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

    public function logout(){
        Session::start();

        // Limpiar Token en BD y Cookie
        if (Session::has('id_user')) {
            User::updateRememberToken(Session::get('id_user'), null);
        }
        if (isset($_COOKIE['remember_me'])) {
            setcookie('remember_me', '', time() - 3600, "/");
        }

        Session::destroy();
        header("Location: /");
        exit;
    }
}