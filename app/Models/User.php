<?php

namespace Models;

use Core\Database;
use PDO;

class User{

    public static function findByCedula($cedula){
        $db = Database::getInstance();
        $query = "SELECT id_user, cedula, password, status FROM user WHERE cedula = :cedula LIMIT 1";
        $stmt = $db->prepare($query);

        $stmt->bindParam(':cedula', $cedula, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}