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

        $viewPath = '/var/www/html/app/View/auth/login.php';

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

            $viewPath = '/var/www/html/app/View/auth/login.php';

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

    public function showForgotPassword() {
        $viewPath = __DIR__ . '/../View/auth/forgot-password.php';
        require_once $viewPath;
    }

    public function sendResetLink() {
        $cedula = $_POST['cedula'] ?? '';
        $user = User::findByCedula($cedula);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));
            
            User::saveResetToken($cedula, $token, $expiresAt);
            
             $resetLink = "http://localhost/restablecer?token=$token";

            // Simulación de envío de correo vía error_log por arquitectura PSR-3/Log
            error_log("RESTABLECER CLAVE - Usuario: $cedula - Link desde el local: $resetLink");


            // =========================================================
            // NUEVO: INTEGRACIÓN REAL DE CORREO (PHPMailer + AWS SES)
            // =========================================================
            try {
                $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                
                // Configuración del servidor SMTP (Lee de tu archivo .env)
                $mail->isSMTP();
                $mail->Host       = $_ENV['SMTP_HOST'];
                $mail->SMTPAuth   = true;
                $mail->Username   = $_ENV['SMTP_USER'];
                $mail->Password   = $_ENV['SMTP_PASS'];
                $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = $_ENV['SMTP_PORT'];


                // Remitente y Destinatario
                $mail->setFrom($_ENV['SMTP_FROM_EMAIL'], $_ENV['SMTP_FROM_NAME']);
                
                $correoDestino = $user['recovery_email'] ?? null; 
                
                if ($correoDestino) {
                    $mail->addAddress($correoDestino);

                    // Contenido del correo (Diseño HTML UPTVal)
                    $mail->isHTML(true);
                    $mail->CharSet = 'UTF-8';
                    $mail->Subject = 'Recuperación de Contraseña - Gestión Académica UPTVal';
                    $mail->Body    = "
                        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;'>
                            <div style='background-color: #0f172a; padding: 20px; text-align: center;'>
                                <h2 style='color: white; margin: 0;'><span style='color: #d97b29;'>UPT</span>Val</h2>
                            </div>
                            <div style='padding: 30px; background-color: #ffffff; color: #333333;'>
                                <p>Hola,</p>
                                <p>Hemos recibido una solicitud para restablecer la contraseña de su cuenta asociada a la cédula <strong>{$cedula}</strong>.</p>
                                <p>Haga clic en el siguiente botón para crear una nueva contraseña. Por seguridad, este enlace expirará en 15 minutos:</p>
                                <div style='text-align: center; margin: 30px 0;'>
                                    <a href='{$resetLink}' style='background-color: #d97b29; color: white; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: bold;'>Restablecer Contraseña</a>
                                </div>
                                <p style='font-size: 12px; color: #999; text-align: center;'>Si usted no solicitó este cambio, ignore este correo de forma segura.</p>
                            </div>
                        </div>";

                    $mail->send();
                } else {
                    error_log("Error AWS SES: El usuario $cedula no tiene un correo registrado en la base de datos.");
                }
            } catch (\Exception $e) {
                // Capturamos el error silenciosamente en el log
                error_log("Fallo al enviar correo con AWS SES: " . $mail->ErrorInfo);
            }
            // =========================================================

            
            $success_message = "Si la cédula es válida, recibirá instrucciones en su correo electrónico.";
        } else {
            $error_message = "Si la cédula es válida, recibirá instrucciones en su correo electrónico.";
        }

        require_once __DIR__ . '/../View/auth/forgot-password.php';
    }

    public function showResetPassword() {
        $token = $_GET['token'] ?? '';
       
        $user = User::findByValidResetToken($token);

        if (empty($token) || !User::findByValidResetToken($token)) {
            die('Error: El token de recuperación es inválido o ha expirado.');
        }
        

        require_once __DIR__ . '/../View/auth/reset-password.php';
    }

    public function resetPassword() {
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($token) || !($user = User::findByValidResetToken($token))) {
            $error_message = "El token ha expirado durante el proceso.";
            require_once __DIR__ . '/../View/auth/forgot-password.php';
            return;
        }

        if ($password !== $confirm_password) {
            $error_message = "Las contraseñas no coinciden.";
            require_once __DIR__ . '/../View/auth/reset-password.php';
            return;
        }

        if (strlen($password) < 8) {
            $error_message = "La contraseña debe tener al menos 8 caracteres.";
            require_once __DIR__ . '/../View/auth/reset-password.php';
            return;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        User::updatePasswordAndBurnToken($user['id_user'], $hashedPassword);

        $success_message = "Contraseña actualizada con éxito. Ya puede iniciar sesión.";
        require_once __DIR__ . '/../View/auth/login.php';
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