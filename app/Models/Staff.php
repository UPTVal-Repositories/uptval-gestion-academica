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
	                u.cedula 
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
}