<?php
namespace Models;
use PDO;
use Core\Database;

class TypeCondition{

    public static function all(){
        $db = Database::getInstance();
        $query = "SELECT id_condition, name, status, description 
                  FROM type_condition 
                  WHERE status = 'activo'
                  ORDER BY name ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAll(){
        $db = Database::getInstance();
        $query = "SELECT id_condition, name, status, description 
                  FROM type_condition 
                  ORDER BY name ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}