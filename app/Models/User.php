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

    public static function updateRememberToken($idUser, $token){
        $db = Database::getInstance();
        $query = "UPDATE user SET remember_token = :token WHERE id_user = :id_user";
        $stmt = $db->prepare($query);

        $stmt->bindValue(':token', $token, $token === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':id_user', $idUser, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public static function findByRememberToken($token){
        $db = Database::getInstance();
        $query = "SELECT id_user, cedula, status FROM user WHERE remember_token = :token LIMIT 1";
        $stmt = $db->prepare($query);

        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}