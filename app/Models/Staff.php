<?php
namespace Models;
use PDO;
use Core\Database;

class Staff{

    //metodo para obtener todo el personal.
    public static function all(){

        $db = Database::getInstance();

        $query = "SELECT 
	                s.id_staff,
	                s.first_name,
                    s.last_name,
	                s.sex,
	                s.phone,
	                s.email,
	                s.type_staff,
	                s.status,
	                d.name,
	                u.cedula,
                    s.pas 
                FROM 
	                staff s 
                INNER JOIN `user` u on 
	                s.id_user = u.id_user
	            INNER JOIN department d on
	            	s.id_department = d.id_department 
                ORDER BY
                    s.last_name ASC";
        
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 1. Método para contar el total de registros
    public static function countAll() {

        $db = Database::getInstance();
        $query = "SELECT 
                    COUNT(s.id_staff) as total 
                  FROM 
                    staff s";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
    }

    public static function paginate($limit, $offset) {
        $db = Database::getInstance();
        
        $sql = "SELECT 
	                u.cedula,
	                s.first_name,
	                s.last_name,
	                s.email,
	                s.type_staff,
	                d.name,
	                s.status,
	                s.sex,
	                s.phone,
	                s.id_staff,
                    s.pas 
                FROM 
	                staff s 
                INNER JOIN `user` u on s.id_user = u.id_user
                INNER JOIN department d on s.id_department = d.id_department 
                LIMIT :limit OFFSET :offset;"; 
                
        $stmt = $db->prepare($sql);
        // Usamos bindValue asegurando que sean enteros, PDO requiere esto para LIMIT
        $stmt->bindValue(':limit', (int) $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Método analítico para el Dashboard
    public static function getDashboardStats() {
        $db = Database::getInstance();
         
        $query = "SELECT
	                COUNT(id_staff) as total,
	                SUM(CASE WHEN status = 'activo' THEN 1 ELSE 0 END) as activos,
	                SUM(CASE WHEN status = 'inactivo' THEN 1 ELSE 0 END) as inactivos
                  FROM
	                staff";
                    
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}