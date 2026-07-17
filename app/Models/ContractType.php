<?php
namespace Models;
use PDO;
use Core\Database;

class ContractType{

    public static function all(){
        $db = Database::getInstance();
        $query = "SELECT id_contract, name, status, description 
                  FROM contract_type 
                  WHERE status = 'activo'
                  ORDER BY name ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAll(){
        $db = Database::getInstance();
        $query = "SELECT id_contract, name, status, description 
                  FROM contract_type 
                  ORDER BY name ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}