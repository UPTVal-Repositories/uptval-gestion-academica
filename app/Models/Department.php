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

    public static function availableForCoordinator() {
        $db = Database::getInstance();
        $query = "SELECT d.id_department, d.name
                  FROM department d
                  WHERE d.status = 'activo'
                    AND NOT EXISTS (
                        SELECT 1
                        FROM rol_user ru
                        INNER JOIN rol r ON ru.id_rol = r.id_rol
                        WHERE ru.id_department = d.id_department
                          AND ru.assignment_state = 'activo'
                          AND r.name = 'Coordinador'
                    )
                  ORDER BY d.name ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById($idDepartment) {
        $db = Database::getInstance();
        $query = "SELECT id_department, name, status
                  FROM department
                  WHERE id_department = :id";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id', (int) $idDepartment, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findWithCoordinator($idDepartment) {
        $db = Database::getInstance();
        $query = "SELECT
                    d.id_department,
                    d.name,
                    d.status,
                    ru.assignment_date AS coordinator_assignment_date,
                    cu.cedula AS coordinator_cedula,
                    cs.first_name AS coordinator_first_name,
                    cs.last_name AS coordinator_last_name
                  FROM
                    department d
                  LEFT JOIN
                    rol_user ru ON ru.id_department = d.id_department AND ru.assignment_state = 'activo'
                  LEFT JOIN
                    rol r ON r.id_rol = ru.id_rol AND r.name = 'Coordinador'
                  LEFT JOIN
                    `user` cu ON cu.id_user = ru.id_user
                  LEFT JOIN
                    staff cs ON cs.id_user = cu.id_user
                  WHERE
                    d.id_department = :id
                  LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id', (int) $idDepartment, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function directory() {
        $db = Database::getInstance();
        $query = "SELECT
                    d.id_department,
                    d.name,
                    d.status,
                    COUNT(DISTINCT s.id_staff) AS staff_count,
                    ru.assignment_date AS coordinator_assignment_date,
                    cu.cedula AS coordinator_cedula,
                    cs.first_name AS coordinator_first_name,
                    cs.last_name AS coordinator_last_name
                  FROM
                    department d
                  LEFT JOIN
                    staff s ON s.id_department = d.id_department
                  LEFT JOIN
                    rol_user ru ON ru.id_department = d.id_department AND ru.assignment_state = 'activo'
                  LEFT JOIN
                    rol r ON r.id_rol = ru.id_rol AND r.name = 'Coordinador'
                  LEFT JOIN
                    `user` cu ON cu.id_user = ru.id_user
                  LEFT JOIN
                    staff cs ON cs.id_user = cu.id_user
                  GROUP BY
                    d.id_department, d.name, d.status,
                    ru.assignment_date, cu.cedula, cs.first_name, cs.last_name
                  ORDER BY
                    d.name ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}