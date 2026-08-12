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

    public static function directory() {
        $db = Database::getInstance();
        $query = "SELECT
                    d.id_department,
                    d.name,
                    d.status,
                    COUNT(s.id_staff) AS staff_count
                  FROM
                    department d
                  LEFT JOIN
                    staff s ON s.id_department = d.id_department
                  GROUP BY
                    d.id_department, d.name, d.status
                  ORDER BY
                    d.name ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}