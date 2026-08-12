<?php

namespace Models;

use PDO;
use Core\Database;

class StaffMateria
{
    private static $baseSelect = "SELECT
                sm.id_staff_materia,
                sm.id_staff,
                sm.id_materia,
                sm.assignment_state,
                sm.assignment_date,
                s.first_name,
                s.last_name,
                s.email,
                u.cedula,
                m.codigo,
                m.name as materia_name,
                m.duracion,
                m.status as materia_status,
                t.name as trayecto_name,
                t.id_trayecto,
                e.name as especialidad_name,
                e.id_especialidad
            FROM
                staff_materia sm
            INNER JOIN staff s ON sm.id_staff = s.id_staff
            INNER JOIN `user` u ON s.id_user = u.id_user
            INNER JOIN materia m ON sm.id_materia = m.id_materia
            INNER JOIN trayecto t ON m.id_trayecto = t.id_trayecto
            LEFT JOIN especialidad e ON m.id_especialidad = e.id_especialidad";

    public static function countFilter($filters = [])
    {
        $db = Database::getInstance();
        $where = [];
        $params = [];

        if (!empty($filters['id_materia'])) {
            $where[] = "sm.id_materia = :materia";
            $params[':materia'] = $filters['id_materia'];
        }
        if (!empty($filters['id_trayecto'])) {
            $where[] = "m.id_trayecto = :trayecto";
            $params[':trayecto'] = $filters['id_trayecto'];
        }
        if (!empty($filters['id_especialidad'])) {
            $where[] = "m.id_especialidad = :especialidad";
            $params[':especialidad'] = $filters['id_especialidad'];
        }
        if (!empty($filters['state'])) {
            $where[] = "sm.assignment_state = :state";
            $params[':state'] = $filters['state'];
        }
        if (!empty($filters['search'])) {
            $where[] = "(u.cedula LIKE :search OR s.first_name LIKE :search OR s.last_name LIKE :search OR m.name LIKE :search OR m.codigo LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql = "SELECT COUNT(sm.id_staff_materia) as total
                FROM staff_materia sm
                INNER JOIN staff s ON sm.id_staff = s.id_staff
                INNER JOIN `user` u ON s.id_user = u.id_user
                INNER JOIN materia m ON sm.id_materia = m.id_materia
                INNER JOIN trayecto t ON m.id_trayecto = t.id_trayecto
                LEFT JOIN especialidad e ON m.id_especialidad = e.id_especialidad";

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $stmt = $db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public static function filter($filters = [], $limit = null, $offset = null)
    {
        $db = Database::getInstance();
        $where = [];
        $params = [];

        if (!empty($filters['id_materia'])) {
            $where[] = "sm.id_materia = :materia";
            $params[':materia'] = $filters['id_materia'];
        }
        if (!empty($filters['id_trayecto'])) {
            $where[] = "m.id_trayecto = :trayecto";
            $params[':trayecto'] = $filters['id_trayecto'];
        }
        if (!empty($filters['id_especialidad'])) {
            $where[] = "m.id_especialidad = :especialidad";
            $params[':especialidad'] = $filters['id_especialidad'];
        }
        if (!empty($filters['state'])) {
            $where[] = "sm.assignment_state = :state";
            $params[':state'] = $filters['state'];
        }
        if (!empty($filters['search'])) {
            $where[] = "(u.cedula LIKE :search OR s.first_name LIKE :search OR s.last_name LIKE :search OR m.name LIKE :search OR m.codigo LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql = self::$baseSelect;

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY u.cedula ASC, m.name ASC";

        if ($limit !== null && $offset !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }

        if ($limit !== null && $offset !== null) {
            $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAssignmentsByStaff($idStaff)
    {
        $db = Database::getInstance();
        $query = "SELECT sm.id_staff_materia, sm.id_materia, sm.assignment_state,
                         m.name, m.codigo, m.duracion, t.name as trayecto_name, e.name as especialidad_name
                  FROM staff_materia sm
                  INNER JOIN materia m ON sm.id_materia = m.id_materia
                  INNER JOIN trayecto t ON m.id_trayecto = t.id_trayecto
                  LEFT JOIN especialidad e ON m.id_especialidad = e.id_especialidad
                  WHERE sm.id_staff = :id_staff
                  ORDER BY t.id_trayecto ASC, e.name ASC, m.name ASC";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id_staff', (int) $idStaff, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getActiveAssignmentsByStaff($idStaff)
    {
        $db = Database::getInstance();
        $query = "SELECT sm.id_staff_materia, sm.id_materia, m.name, m.codigo, m.duracion, t.name as trayecto_name, e.name as especialidad_name
                  FROM staff_materia sm
                  INNER JOIN materia m ON sm.id_materia = m.id_materia
                  INNER JOIN trayecto t ON m.id_trayecto = t.id_trayecto
                  LEFT JOIN especialidad e ON m.id_especialidad = e.id_especialidad
                  WHERE sm.id_staff = :id_staff AND sm.assignment_state = 'activo'
                  ORDER BY t.id_trayecto ASC, e.name ASC, m.name ASC";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id_staff', (int) $idStaff, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getMateriaIdsByStaff($idStaff)
    {
        $db = Database::getInstance();
        $query = "SELECT sm.id_materia
                  FROM staff_materia sm
                  WHERE sm.id_staff = :id_staff AND sm.assignment_state = 'activo'";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id_staff', (int) $idStaff, PDO::PARAM_INT);
        $stmt->execute();
        return array_map('intval', array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id_materia'));
    }

    public static function findAssignment($idStaffMateria)
    {
        $db = Database::getInstance();
        $query = "SELECT sm.id_staff_materia, sm.id_staff, sm.id_materia, sm.assignment_state,
                         s.first_name, s.last_name, u.cedula, m.name as materia_name
                  FROM staff_materia sm
                  INNER JOIN staff s ON sm.id_staff = s.id_staff
                  INNER JOIN `user` u ON s.id_user = u.id_user
                  INNER JOIN materia m ON sm.id_materia = m.id_materia
                  WHERE sm.id_staff_materia = :id
                  LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id', (int) $idStaffMateria, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private static function findStaffMateria($idStaff, $idMateria)
    {
        $db = Database::getInstance();
        $query = "SELECT id_staff_materia, assignment_state
                  FROM staff_materia
                  WHERE id_staff = :id_staff AND id_materia = :id_materia
                  LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id_staff', (int) $idStaff, PDO::PARAM_INT);
        $stmt->bindValue(':id_materia', (int) $idMateria, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function assign($idStaff, $idMateria)
    {
        $db = Database::getInstance();
        $existing = self::findStaffMateria($idStaff, $idMateria);

        if ($existing) {
            if ($existing['assignment_state'] === 'inactivo') {
                $query = "UPDATE staff_materia
                          SET assignment_state = 'activo',
                              assignment_date = NOW()
                          WHERE id_staff_materia = :id";
                $stmt = $db->prepare($query);
                $stmt->bindValue(':id', (int) $existing['id_staff_materia'], PDO::PARAM_INT);
                $stmt->execute();
                return 'reactivated';
            }
            return 'already';
        }

        $query = "INSERT INTO staff_materia (id_staff, id_materia, assignment_state, assignment_date)
                  VALUES (:id_staff, :id_materia, 'activo', NOW())";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id_staff', (int) $idStaff, PDO::PARAM_INT);
        $stmt->bindValue(':id_materia', (int) $idMateria, PDO::PARAM_INT);
        $stmt->execute();
        return 'assigned';
    }

    public static function delete($idStaffMateria)
    {
        $db = Database::getInstance();
        $query = "DELETE FROM staff_materia WHERE id_staff_materia = :id";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id', (int) $idStaffMateria, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public static function setState($idStaffMateria, $state)
    {
        $db = Database::getInstance();
        $query = "UPDATE staff_materia SET assignment_state = :state WHERE id_staff_materia = :id";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':state', $state);
        $stmt->bindValue(':id', (int) $idStaffMateria, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
