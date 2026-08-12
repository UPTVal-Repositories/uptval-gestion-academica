<?php

namespace Models;

use Core\Database;
use PDO;

class RolUser
{
    private static $baseSelect = "SELECT 
                ru.id_rol_user,
                ru.id_user,
                ru.id_rol,
                ru.assignment_state,
                ru.assignment_date,
                ru.id_department,
                r.name as rol_name,
                u.cedula,
                u.status as user_status,
                s.first_name,
                s.last_name,
                s.email,
                sd.name as department_name,
                d.name as assignment_department_name
            FROM 
                rol_user ru
            INNER JOIN rol r ON ru.id_rol = r.id_rol
            INNER JOIN `user` u ON ru.id_user = u.id_user
            LEFT JOIN staff s ON s.id_user = u.id_user
            LEFT JOIN department sd ON s.id_department = sd.id_department
            LEFT JOIN department d ON ru.id_department = d.id_department";

    public static function getRolesByUserId($idUser)
    {
        $db = Database::getInstance();
        $query = "SELECT r.name
                  FROM rol_user ru
                  INNER JOIN rol r ON ru.id_rol = r.id_rol
                  WHERE ru.id_user = :id_user AND ru.assignment_state = 'activo'";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id_user', (int) $idUser, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_column($rows, 'name');
    }

    public static function getRoleIdsByUserId($idUser)
    {
        $db = Database::getInstance();
        $query = "SELECT ru.id_rol
                  FROM rol_user ru
                  WHERE ru.id_user = :id_user AND ru.assignment_state = 'activo'";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id_user', (int) $idUser, PDO::PARAM_INT);
        $stmt->execute();
        return array_map('intval', array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id_rol'));
    }

    public static function countFilter($filters = []) {
        $db = Database::getInstance();
        $where = [];
        $params = [];

        if (!empty($filters['id_rol'])) {
            $where[] = "ru.id_rol = :rol";
            $params[':rol'] = $filters['id_rol'];
        }
        if (!empty($filters['state'])) {
            $where[] = "ru.assignment_state = :state";
            $params[':state'] = $filters['state'];
        }
        if (!empty($filters['search'])) {
            $where[] = "(u.cedula LIKE :search OR s.first_name LIKE :search OR s.last_name LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql = "SELECT COUNT(ru.id_rol_user) as total
                FROM rol_user ru
                INNER JOIN rol r ON ru.id_rol = r.id_rol
                INNER JOIN `user` u ON ru.id_user = u.id_user
                LEFT JOIN staff s ON s.id_user = u.id_user";

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

    public static function filter($filters = [], $limit = null, $offset = null) {
        $db = Database::getInstance();
        $where = [];
        $params = [];

        if (!empty($filters['id_rol'])) {
            $where[] = "ru.id_rol = :rol";
            $params[':rol'] = $filters['id_rol'];
        }
        if (!empty($filters['state'])) {
            $where[] = "ru.assignment_state = :state";
            $params[':state'] = $filters['state'];
        }
        if (!empty($filters['search'])) {
            $where[] = "(u.cedula LIKE :search OR s.first_name LIKE :search OR s.last_name LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql = self::$baseSelect;

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY u.cedula ASC";

        if ($limit !== null && $offset !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }

        if ($limit !== null && $offset !== null) {
            $stmt->bindValue(':limit', (int) $limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int) $offset, \PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findAssignment($idRolUser) {
        $db = Database::getInstance();
        $query = "SELECT ru.id_rol_user, ru.id_user, ru.id_rol, ru.id_department, ru.assignment_state, ru.assignment_date, r.name AS rol_name
                  FROM rol_user ru
                  INNER JOIN rol r ON ru.id_rol = r.id_rol
                  WHERE ru.id_rol_user = :id";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id', (int) $idRolUser, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private static function findUserRol($idUser, $idRol) {
        $db = Database::getInstance();
        $query = "SELECT id_rol_user, assignment_state
                  FROM rol_user
                  WHERE id_user = :id_user AND id_rol = :id_rol";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id_user', (int) $idUser, PDO::PARAM_INT);
        $stmt->bindValue(':id_rol', (int) $idRol, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function assign($idUser, $idRol, $rolName = null, $idDepartment = null) {
        $db = Database::getInstance();

        if ($rolName === 'Coordinador' && $idDepartment !== null) {
            $db->beginTransaction();
            try {
                if (self::countActiveCoordinators($idDepartment, $idUser) > 0) {
                    $db->rollBack();
                    return 'department_taken';
                }
                $existing = self::findUserRol($idUser, $idRol);
                $result = self::doAssign($db, $existing, $idUser, $idRol, $idDepartment);
                $db->commit();
                return $result;
            } catch (\Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }

        $existing = self::findUserRol($idUser, $idRol);
        return self::doAssign($db, $existing, $idUser, $idRol, $idDepartment);
    }

    private static function doAssign($db, $existing, $idUser, $idRol, $idDepartment) {
        if ($existing) {
            if ($existing['assignment_state'] === 'activo') {
                return 'already';
            }
            $query = "UPDATE rol_user
                      SET assignment_state = 'activo', assignment_date = NOW(), id_department = :dept
                      WHERE id_rol_user = :id";
            $stmt = $db->prepare($query);
            $stmt->bindValue(':dept', $idDepartment !== null ? (int) $idDepartment : null, \PDO::PARAM_INT);
            $stmt->bindValue(':id', (int) $existing['id_rol_user'], \PDO::PARAM_INT);
            $stmt->execute();
            return 'reactivated';
        }

        $query = "INSERT INTO rol_user (id_user, id_rol, id_department, assignment_state, assignment_date)
                  VALUES (:id_user, :id_rol, :dept, 'activo', NOW())";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id_user', (int) $idUser, \PDO::PARAM_INT);
        $stmt->bindValue(':id_rol', (int) $idRol, \PDO::PARAM_INT);
        $stmt->bindValue(':dept', $idDepartment !== null ? (int) $idDepartment : null, \PDO::PARAM_INT);
        $stmt->execute();
        return 'assigned';
    }

    public static function countActiveCoordinators($idDepartment, $excludeUserId = null) {
        $db = Database::getInstance();
        $query = "SELECT COUNT(*) AS total
                  FROM rol_user ru
                  INNER JOIN rol r ON ru.id_rol = r.id_rol
                  WHERE r.name = 'Coordinador'
                    AND ru.id_department = :dept
                    AND ru.assignment_state = 'activo'";
        $params = [':dept' => (int) $idDepartment];
        if ($excludeUserId !== null) {
            $query .= " AND ru.id_user != :exclude";
            $params[':exclude'] = (int) $excludeUserId;
        }
        $stmt = $db->prepare($query);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, \PDO::PARAM_INT);
        }
        $stmt->execute();
        return (int) $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
    }

    public static function deactivate($idRolUser) {
        $db = Database::getInstance();
        $query = "UPDATE rol_user
                  SET assignment_state = 'historico'
                  WHERE id_rol_user = :id AND assignment_state = 'activo'";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id', (int) $idRolUser, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public static function reactivate($idRolUser) {
        $db = Database::getInstance();
        $assignment = self::findAssignment((int) $idRolUser);

        if (!$assignment) {
            return 'not_found';
        }

        if (!empty($assignment['id_department'])) {
            if (self::countActiveCoordinators((int) $assignment['id_department'], (int) $assignment['id_user']) > 0) {
                return 'department_taken';
            }
        }

        $query = "UPDATE rol_user
                  SET assignment_state = 'activo', assignment_date = NOW()
                  WHERE id_rol_user = :id AND assignment_state = 'historico'";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id', (int) $idRolUser, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 'ok' : 'not_found';
    }

    public static function countActiveAdmins() {
        $db = Database::getInstance();
        $query = "SELECT COUNT(*) AS total
                  FROM rol_user ru
                  INNER JOIN rol r ON ru.id_rol = r.id_rol
                  WHERE r.name = 'Administrador' AND ru.assignment_state = 'activo'";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
