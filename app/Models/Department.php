<?php

namespace Models;
use PDO;
use Core\Database;

class Department{

    public static function all() {
        $db = Database::getInstance();
        $query = "SELECT 
	                d.id_department, 
	                d.name
                  FROM
	                department d
                  WHERE
	                d.status = 'activo'
                  ORDER BY
	              name ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}