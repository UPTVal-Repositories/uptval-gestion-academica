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

    }
}