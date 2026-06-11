<?php

namespace Controllers;

use Core\Session;

class DashboardController {

    public function index() {
        
        // 1. Capa de Seguridad: Verificamos si el usuario NO está logueado
        if (!Session::has('id_user')) {
            // Si no hay sesión, lo devolvemos al login de inmediato
            header("Location: /");
            exit;
        }

        // 2. Si pasa la validación, extraemos sus datos de la sesión
        $idUser = Session::get('id_user');
        $cedula = Session::get('cedula');

       /* // 3. Renderizamos una vista temporal para confirmar que funcionó
        // (Más adelante aquí haremos el require_once de tu vista HTML real del dashboard)
        echo "<div style='background-color: #0f172a; color: white; min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; font-family: sans-serif;'>";
        echo "<h1 style='color: #d97b29;'>UPTVal - Dashboard</h1>";
        echo "<h2>¡Autenticación y Sesión Exitosas!</h2>";
        echo "<p>Cédula del usuario activo: <strong>{$cedula}</strong></p>";
        echo "<p>ID interno: <strong>{$idUser}</strong></p>";
        echo "<a href='/logout' style='color: #808285; margin-top: 20px;'>Cerrar Sesión (Próximamente)</a>";
        echo "</div>";*/

        $viewPath = __DIR__ . '/../View/dashboard/index.php';
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("Error: No se encontró la vista en la ruta calculada: " . $viewPath);
        }
    }
}