<?php

namespace Controllers;

use Core\Session;

class DocumentacionController
{
    public function index()
    {
        if (!Session::has('id_user')) {
            header("Location: /login");
            exit;
        }

        $cedula = Session::get('cedula');
        $userRoles = Session::get('user_roles') ?? [];

        require_once __DIR__ . '/../View/documentacion/index.php';
    }
}
