<?php

namespace Models;

use PDO;
use Core\Database;

class Trayecto
{
    public static function all()
    {
        $db = Database::getInstance();
        $query = "SELECT id_trayecto, name, status, created_at, updated_at
                  FROM trayecto
                  ORDER BY FIELD(name, 'Trayecto Inicial') DESC, id_trayecto ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById($id)
    {
        $db = Database::getInstance();
        $query = "SELECT id_trayecto, name, status FROM trayecto WHERE id_trayecto = :id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findByName($name)
    {
        $db = Database::getInstance();
        $query = "SELECT id_trayecto, name FROM trayecto WHERE name = :name LIMIT 1";
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
            $where[] = "t.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $where[] = "t.name LIKE :search";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql = "SELECT COUNT(t.id_trayecto) as total FROM trayecto t";

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
            $where[] = "t.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $where[] = "t.name LIKE :search";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql = "SELECT t.id_trayecto, t.name, t.status, t.created_at, t.updated_at FROM trayecto t";

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY FIELD(t.name, 'Trayecto Inicial') DESC, t.id_trayecto ASC";

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
        $query = "INSERT INTO trayecto (name, status) VALUES (:name, 'activo')";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':name', $name);
        $stmt->execute();
        return (int) $db->lastInsertId();
    }

    public static function update($id, $name)
    {
        $db = Database::getInstance();
        $query = "UPDATE trayecto SET name = :name WHERE id_trayecto = :id";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function setState($id, $state)
    {
        $db = Database::getInstance();
        $query = "UPDATE trayecto SET status = :state WHERE id_trayecto = :id";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':state', $state);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function delete($id)
    {
        $db = Database::getInstance();
        $query = "DELETE FROM trayecto WHERE id_trayecto = :id";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public static function countMaterias($idTrayecto)
    {
        $db = Database::getInstance();
        $query = "SELECT COUNT(*) AS total FROM materia WHERE id_trayecto = :id";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id', (int) $idTrayecto, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
