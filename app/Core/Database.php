<?php

namespace Core;

use PDO;
use PDOException;

class Database{

    private static $instance = null;
    private $pdo;

    private function __construct(){

        $host = getenv('DB_HOST') ?: 'db';
        $db   = getenv('DB_DATABASE') ?: 'gestion_academica';
        $user = getenv('DB_USERNAME') ?: 'db_user';
        $pass = getenv('DB_PASSWORD') ?: 'db_password';

        $dns = "mysql:host=$host;dbname=$db;charset=utf8mb4";

        try{

            $this->pdo = new PDO($dns, $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        }catch(PDOException $e){

          die("Error critico de conexion a la base de datos: " . $e->getMessage());  
        }
    }

    public static function getInstance(){

        //si la instancia no existe creamos la instancia una unica vez
        if(self::$instance == null){
            self::$instance = new Database();
        }

        return self::$instance->pdo;
    }
    
}