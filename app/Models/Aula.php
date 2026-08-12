<?php

namespace Models;

use PDO;
use Core\Database;

class Aula
{
    private static $baseSelect = "SELECT
                a.id_aula,
                a.code,
                a.name,
                a.type,
                a.status,
                a.id_department,
                a.created_at,
                a.updated_at,
                d.name as department_name
            FROM
                aula a
            INNER JOIN department d ON a.id_department = d.id_department";

    public static function countFilter($filters = [])
    {
        $db = Database::getInstance();
        $where = [];
        $params = [];

        if (!empty($filters['id_department'])) {
            $where[] = "a.id_department = :department";
            $params[':department'] = $filters['id_department'];
        }
        if (!empty($filters['type'])) {
            $where[] = "a.type = :type";
            $params[':type'] = $filters['type'];
        }
        if (!empty($filters['status'])) {
            $where[] = "a.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $where[] = "(a.code LIKE :search OR a.name LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql = "SELECT COUNT(a.id_aula) as total
                FROM aula a
                INNER JOIN department d ON a.id_department = d.id_department";

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

        if (!empty($filters['id_department'])) {
            $where[] = "a.id_department = :department";
            $params[':department'] = $filters['id_department'];
        }
        if (!empty($filters['type'])) {
            $where[] = "a.type = :type";
            $params[':type'] = $filters['type'];
        }
        if (!empty($filters['status'])) {
            $where[] = "a.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $where[] = "(a.code LIKE :search OR a.name LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql = self::$baseSelect;

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY a.type ASC, a.code ASC";

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

    public static function findById($id)
    {
        $db = Database::getInstance();
        $query = self::$baseSelect . " WHERE a.id_aula = :id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findByCode($code)
    {
        $db = Database::getInstance();
        $query = "SELECT id_aula, code FROM aula WHERE code = :code LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':code', $code);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data)
    {
        $db = Database::getInstance();
        $query = "INSERT INTO aula (code, name, type, id_department, status)
                  VALUES (:code, :name, :type, :department, 'activo')";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':code', $data['code']);
        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':type', $data['type']);
        $stmt->bindValue(':department', (int) $data['id_department'], PDO::PARAM_INT);
        $stmt->execute();
        return (int) $db->lastInsertId();
    }

    public static function update($id, $data)
    {
        $db = Database::getInstance();
        $query = "UPDATE aula SET
                    code          = :code,
                    name          = :name,
                    type          = :type,
                    id_department = :department
                  WHERE id_aula = :id";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':code', $data['code']);
        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':type', $data['type']);
        $stmt->bindValue(':department', (int) $data['id_department'], PDO::PARAM_INT);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function setState($id, $state)
    {
        $db = Database::getInstance();
        $query = "UPDATE aula SET status = :state WHERE id_aula = :id";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':state', $state);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function delete($id)
    {
        $db = Database::getInstance();
        $query = "DELETE FROM aula WHERE id_aula = :id";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
