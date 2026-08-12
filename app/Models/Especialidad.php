<?php

namespace Models;

use PDO;
use Core\Database;

class Especialidad
{
    public static function all()
    {
        $db = Database::getInstance();
        $query = "SELECT id_especialidad, name, status, created_at, updated_at
                  FROM especialidad
                  ORDER BY name ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function allActive()
    {
        $db = Database::getInstance();
        $query = "SELECT id_especialidad, name, status
                  FROM especialidad
                  WHERE status = 'activo'
                  ORDER BY name ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById($id)
    {
        $db = Database::getInstance();
        $query = "SELECT id_especialidad, name, status FROM especialidad WHERE id_especialidad = :id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findByName($name)
    {
        $db = Database::getInstance();
        $query = "SELECT id_especialidad, name FROM especialidad WHERE name = :name LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':name', $name);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function countFilter($filters = [])
    {
        $db = Database::getInstance();
        $where = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = "e.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $where[] = "e.name LIKE :search";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql = "SELECT COUNT(e.id_especialidad) as total FROM especialidad e";

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

        if (!empty($filters['status'])) {
            $where[] = "e.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $where[] = "e.name LIKE :search";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql = "SELECT e.id_especialidad, e.name, e.status, e.created_at, e.updated_at FROM especialidad e";

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY e.name ASC";

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

    public static function create($name)
    {
        $db = Database::getInstance();
        $query = "INSERT INTO especialidad (name, status) VALUES (:name, 'activo')";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':name', $name);
        $stmt->execute();
        return (int) $db->lastInsertId();
    }

    public static function update($id, $name)
    {
        $db = Database::getInstance();
        $query = "UPDATE especialidad SET name = :name WHERE id_especialidad = :id";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function setState($id, $state)
    {
        $db = Database::getInstance();
        $query = "UPDATE especialidad SET status = :state WHERE id_especialidad = :id";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':state', $state);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function delete($id)
    {
        $db = Database::getInstance();
        $query = "DELETE FROM especialidad WHERE id_especialidad = :id";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public static function countMaterias($idEspecialidad)
    {
        $db = Database::getInstance();
        $query = "SELECT COUNT(*) AS total FROM materia WHERE id_especialidad = :id";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id', (int) $idEspecialidad, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
