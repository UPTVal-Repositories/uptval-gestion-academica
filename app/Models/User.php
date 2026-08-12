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

    public static function findById($idUser){
        $db = Database::getInstance();
        $query = "SELECT id_user, cedula, status FROM user WHERE id_user = :id_user LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id_user', (int) $idUser, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function activeWithRoles() {
        $db = Database::getInstance();
        $query = "SELECT u.id_user, u.cedula, u.status,
                         s.first_name, s.last_name,
                         ru.id_rol
                  FROM user u
                  LEFT JOIN staff s ON s.id_user = u.id_user
                  LEFT JOIN rol_user ru ON ru.id_user = u.id_user AND ru.assignment_state = 'activo'
                  WHERE u.status = 'activo'
                  ORDER BY u.cedula ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $users = [];
        foreach ($rows as $row) {
            $id = $row['id_user'];
            if (!isset($users[$id])) {
                $users[$id] = [
                    'id_user'    => (int) $row['id_user'],
                    'cedula'     => $row['cedula'],
                    'first_name' => $row['first_name'],
                    'last_name'  => $row['last_name'],
                    'role_ids'   => []
                ];
            }
            if ($row['id_rol'] !== null) {
                $users[$id]['role_ids'][] = (int) $row['id_rol'];
            }
        }

        return array_values($users);
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