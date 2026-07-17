<?php
namespace Models;
use PDO;
use Core\Database;

class Staff{

    private static $baseSelect = "SELECT 
                s.id_staff,
                s.first_name,
                s.last_name,
                s.sex,
                s.phone,
                s.email,
                s.type_staff,
                s.pas,
                s.type_condition,
                tc.name as condition_name,
                tc.status as condition_status,
                s.type_contract,
                ct.name as contract_name,
                d.name as department_name,
                u.cedula
            FROM 
                staff s 
            INNER JOIN `user` u ON s.id_user = u.id_user
            INNER JOIN department d ON s.id_department = d.id_department
            LEFT JOIN type_condition tc ON s.type_condition = tc.id_condition
            LEFT JOIN contract_type ct ON s.type_contract = ct.id_contract";

    public static function all(){
        $db = Database::getInstance();
        $query = self::$baseSelect . " ORDER BY s.last_name ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function countAll() {
        $db = Database::getInstance();
        $query = "SELECT COUNT(s.id_staff) as total FROM staff s";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
    }

    public static function paginate($limit, $offset) {
        $db = Database::getInstance();
        $sql = self::$baseSelect . " ORDER BY s.last_name ASC LIMIT :limit OFFSET :offset";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':limit', (int) $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getDashboardStats() {
        $db = Database::getInstance();
        $query = "SELECT
                    COUNT(s.id_staff) as total,
                    SUM(CASE WHEN tc.status = 'activo' THEN 1 ELSE 0 END) as activos,
                    SUM(CASE WHEN tc.status = 'inactivo' THEN 1 ELSE 0 END) as inactivos
                  FROM staff s
                  LEFT JOIN type_condition tc ON s.type_condition = tc.id_condition";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function getStatsByType($departmentId = null, $conditionId = null) {
        $db = Database::getInstance();

        $conditions = [];
        $params = [];

        if (!empty($departmentId)) {
            $conditions[] = "s.id_department = :dept";
            $params[':dept'] = $departmentId;
        }
        if (!empty($conditionId)) {
            $conditions[] = "s.type_condition = :condition";
            $params[':condition'] = $conditionId;
        }

        $whereClause = !empty($conditions) ? 'AND ' . implode(' AND ', $conditions) : '';

        $sql = "SELECT types.pas, COUNT(s.id_staff) as total
                FROM (SELECT 'Docente' as pas UNION SELECT 'Administrativo' UNION SELECT 'Obrero') as types
                LEFT JOIN staff s ON s.pas = types.pas {$whereClause}
                GROUP BY types.pas
                ORDER BY types.pas ASC";

        $stmt = $db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function countFilter($filters = []) {
        $db = Database::getInstance();
        $where = [];
        $params = [];

        if (!empty($filters['id_department'])) {
            $where[] = "s.id_department = :dept";
            $params[':dept'] = $filters['id_department'];
        }
        if (!empty($filters['type_staff'])) {
            $where[] = "s.pas = :type";
            $params[':type'] = $filters['type_staff'];
        }
        if (!empty($filters['id_condition'])) {
            $where[] = "s.type_condition = :condition";
            $params[':condition'] = $filters['id_condition'];
        }
        if (!empty($filters['id_contract'])) {
            $where[] = "s.type_contract = :contract";
            $params[':contract'] = $filters['id_contract'];
        }
        if (!empty($filters['search'])) {
            $where[] = "(u.cedula LIKE :search OR s.first_name LIKE :search OR s.last_name LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql = "SELECT COUNT(s.id_staff) as total 
                FROM staff s 
                INNER JOIN `user` u ON s.id_user = u.id_user
                INNER JOIN department d ON s.id_department = d.id_department
                LEFT JOIN type_condition tc ON s.type_condition = tc.id_condition
                LEFT JOIN contract_type ct ON s.type_contract = ct.id_contract";

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

        if (!empty($filters['id_department'])) {
            $where[] = "s.id_department = :dept";
            $params[':dept'] = $filters['id_department'];
        }
        if (!empty($filters['type_staff'])) {
            $where[] = "s.pas = :type";
            $params[':type'] = $filters['type_staff'];
        }
        if (!empty($filters['id_condition'])) {
            $where[] = "s.type_condition = :condition";
            $params[':condition'] = $filters['id_condition'];
        }
        if (!empty($filters['id_contract'])) {
            $where[] = "s.type_contract = :contract";
            $params[':contract'] = $filters['id_contract'];
        }
        if (!empty($filters['search'])) {
            $where[] = "(u.cedula LIKE :search OR s.first_name LIKE :search OR s.last_name LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql = self::$baseSelect;

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY s.last_name ASC";

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
}