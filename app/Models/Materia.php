<?php

namespace Models;

use PDO;
use Core\Database;

class Materia
{
    private static $baseSelect = "SELECT
                m.id_materia,
                m.codigo,
                m.name,
                m.duracion,
                m.status,
                m.id_trayecto,
                m.id_especialidad,
                m.created_at,
                m.updated_at,
                t.name as trayecto_name,
                t.status as trayecto_status,
                e.name as especialidad_name,
                e.status as especialidad_status
            FROM
                materia m
            INNER JOIN trayecto t ON m.id_trayecto = t.id_trayecto
            LEFT JOIN especialidad e ON m.id_especialidad = e.id_especialidad";

    public static function all()
    {
        $db = Database::getInstance();
        $query = self::$baseSelect . " ORDER BY FIELD(t.name, 'Trayecto Inicial') DESC, t.id_trayecto ASC, e.name ASC, m.name ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function allActive()
    {
        $db = Database::getInstance();
        $query = self::$baseSelect . " WHERE m.status = 'activo' ORDER BY FIELD(t.name, 'Trayecto Inicial') DESC, t.id_trayecto ASC, e.name ASC, m.name ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById($id)
    {
        $db = Database::getInstance();
        $query = self::$baseSelect . " WHERE m.id_materia = :id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findByCodigo($codigo)
    {
        $db = Database::getInstance();
        $query = "SELECT id_materia, codigo FROM materia WHERE codigo = :codigo LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':codigo', $codigo);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function countFilter($filters = [])
    {
        $db = Database::getInstance();
        $where = [];
        $params = [];

        if (!empty($filters['id_trayecto'])) {
            $where[] = "m.id_trayecto = :trayecto";
            $params[':trayecto'] = $filters['id_trayecto'];
        }
        if (!empty($filters['id_especialidad'])) {
            $where[] = "m.id_especialidad = :especialidad";
            $params[':especialidad'] = $filters['id_especialidad'];
        }
        if (!empty($filters['duracion'])) {
            $where[] = "m.duracion = :duracion";
            $params[':duracion'] = $filters['duracion'];
        }
        if (!empty($filters['status'])) {
            $where[] = "m.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $where[] = "(m.codigo LIKE :search OR m.name LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql = "SELECT COUNT(m.id_materia) as total
                FROM materia m
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

        if (!empty($filters['id_trayecto'])) {
            $where[] = "m.id_trayecto = :trayecto";
            $params[':trayecto'] = $filters['id_trayecto'];
        }
        if (!empty($filters['id_especialidad'])) {
            $where[] = "m.id_especialidad = :especialidad";
            $params[':especialidad'] = $filters['id_especialidad'];
        }
        if (!empty($filters['duracion'])) {
            $where[] = "m.duracion = :duracion";
            $params[':duracion'] = $filters['duracion'];
        }
        if (!empty($filters['status'])) {
            $where[] = "m.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $where[] = "(m.codigo LIKE :search OR m.name LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql = self::$baseSelect;

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY FIELD(t.name, 'Trayecto Inicial') DESC, t.id_trayecto ASC, e.name ASC, m.name ASC";

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

    public static function create($data)
    {
        $db = Database::getInstance();
        $query = "INSERT INTO materia (codigo, name, duracion, id_trayecto, id_especialidad, status)
                  VALUES (:codigo, :name, :duracion, :trayecto, :especialidad, 'activo')";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':codigo', $data['codigo']);
        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':duracion', $data['duracion']);
        $stmt->bindValue(':trayecto', (int) $data['id_trayecto'], PDO::PARAM_INT);
        $stmt->bindValue(':especialidad', !empty($data['id_especialidad']) ? (int) $data['id_especialidad'] : null, !empty($data['id_especialidad']) ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->execute();
        return (int) $db->lastInsertId();
    }

    public static function update($id, $data)
    {
        $db = Database::getInstance();
        $query = "UPDATE materia SET
                    codigo          = :codigo,
                    name            = :name,
                    duracion        = :duracion,
                    id_trayecto     = :trayecto,
                    id_especialidad = :especialidad
                  WHERE id_materia = :id";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':codigo', $data['codigo']);
        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':duracion', $data['duracion']);
        $stmt->bindValue(':trayecto', (int) $data['id_trayecto'], PDO::PARAM_INT);
        $stmt->bindValue(':especialidad', !empty($data['id_especialidad']) ? (int) $data['id_especialidad'] : null, !empty($data['id_especialidad']) ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function setState($id, $state)
    {
        $db = Database::getInstance();
        $query = "UPDATE materia SET status = :state WHERE id_materia = :id";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':state', $state);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function delete($id)
    {
        $db = Database::getInstance();
        $query = "DELETE FROM materia WHERE id_materia = :id";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public static function countDocentes($idMateria)
    {
        $db = Database::getInstance();
        $query = "SELECT COUNT(*) AS total FROM staff_materia WHERE id_materia = :id";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id', (int) $idMateria, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
