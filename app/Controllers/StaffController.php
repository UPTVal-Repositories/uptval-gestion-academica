<?php

namespace Controllers;

use Core\Session;

class StaffController{

    public function index(){

        if(!Session::has('id_user')){
            header("Location: /login");
            exit;
        }

        $cedula = Session::get('cedula');

        $last_connection_raw = Session::get('last_connection');
        $last_connection = '';

        if (!empty($last_connection_raw)) {
            $last_connection = date('d/m/Y - h:i a', strtotime($last_connection_raw));
        }

        require_once __DIR__ . '/../View/staff/index.php';
    }
}