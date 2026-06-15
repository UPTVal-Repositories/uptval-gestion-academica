<?php

namespace Models;

use Core\Database;
use PDO;

class User{

    public static function findByCedula($cedula){
        $db = Database::getInstance();
        $query = "SELECT id_user, cedula, password, status, last_connection, recovery_email FROM user WHERE cedula = :cedula LIMIT 1";
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

    public static function updateLastConnection($idUser){
        $db = Database::getInstance();

        $now = date('Y-m-d H:i:s');

        $query = "UPDATE user SET last_connection = :now WHERE id_user = :id_user";

        $stmt = $db->prepare($query);

        $stmt->bindParam(':now', $now);
        $stmt->bindParam(':id_user', $idUser, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function saveResetToken($cedula, $token, $expiresAt) {
        $db = Database::getInstance();
        $query = "UPDATE user SET reset_token = :token, reset_token_expires_at = :expires, status ='bloqueado' WHERE cedula = :cedula";
        $stmt = $db->prepare($query);
        
        // Usamos bindValue para literales hardcodeados
        $stmt->bindValue(':token', $token);
        $stmt->bindValue(':expires', $expiresAt);
        $stmt->bindValue(':cedula', $cedula);
        
        return $stmt->execute();
    }

    public static function findByValidResetToken($token) {
        $db = Database::getInstance();
        $now = date('Y-m-d H:i:s');
        
        $query = "SELECT id_user, cedula FROM user 
                  WHERE reset_token = :token 
                  AND reset_token_expires_at > :now 
                  AND status = 'bloqueado' 
                  LIMIT 1";
                  
        $stmt = $db->prepare($query);
        $stmt->bindValue(':token', $token); // O bindParam si usas variable
        $stmt->bindValue(':now', $now);
        $stmt->execute();
        
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function updatePasswordAndBurnToken($idUser, $hashedPassword) {
        $db = Database::getInstance();
        $query = "UPDATE user SET password = :password, reset_token = NULL, reset_token_expires_at = NULL, status = 'activo'  WHERE id_user = :id_user";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':id_user', $idUser, PDO::PARAM_INT);
        return $stmt->execute();
    }

    
}