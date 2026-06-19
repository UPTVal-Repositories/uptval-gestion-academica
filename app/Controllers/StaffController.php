<?php

namespace Controllers;

use Core\Session;
use Models\Staff;

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
        
        $staffList = Staff::all();
       /* echo "<pre style='background: #111; color: #0f0; padding: 20px; z-index: 9999; position: relative;'>";
print_r($staffList);
echo "</pre>";
die();*/

        if(!empty($staffList) && isset($staffList['id_staff'])){
            $staffList = [$staffList];
        }

        require_once __DIR__ . '/../View/staff/index.php';
    }
}