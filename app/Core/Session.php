<?php

namespace Core;

class Session{

    public static function start(){
        if(session_status() === PHP_SESSION_NONE){
            //agregar medidas de seguridad para evitar ataques XSS y fijacion de sesion
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_samesite', 'Strict');

            session_start();
        }
    }

    //Guardar el valor de la sesion
    public static function set($key, $value){
        $_SESSION[$key] = $value;
    }

    //obtener el valor de la sesion
    public static function get($key){
        return $_SESSION[$key] ?? null;
    }

    //verifica si existe una clave en la sesion
    public static function has($key){
        return isset($_SESSION[$key]);
    }

    //destruye la sesion por completo cuando finaliza la sesion
    public static function destroy(){
        if(session_status() !== PHP_SESSION_NONE ){

            $_SESSION = [];

            //eliminamos la cookie fisica del navegador
            if(ini_get("session.use_cookies")){
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]);
            }

            session_destroy();
        }
    }
}