<?php

namespace Models;

use PDO;
use Core\Database;

/**
 * Modelo Estudiante (solo lectura)
 * Los estudiantes se registran desde el sistema de inscripcion externo.
 */
class Estudiante
{
    private static $baseSelect = "SELECT
                e.id_estudiante,
                e.cedula,
                e.first_name,
                e.last_name,
                e.sex,
                e.birth_date,
                e.phone,
                e.email,
                e.seccion,
                e.id_trayecto,
                e.id_especialidad,
                e.status,
                e.created_at,
                e.updated_at,
                t.name as trayecto_name,
                es.name as especialidad_name
            FROM
                estudiante e
            INNER JOIN trayecto t ON e.id_trayecto = t.id_trayecto
            LEFT JOIN especialidad es ON e.id_especialidad = es.id_especialidad";

    /**
     * Cuenta estudiantes con filtros aplicados
     */
    public static function countFilter($filters = [])
    {
        $db = Database::getInstance();
        $where = [];
        $params = [];

        if (!empty($filters['id_trayecto'])) {
            $where[] = "e.id_trayecto = :trayecto";
            $params[':trayecto'] = $filters['id_trayecto'];
        }
        if (!empty($filters['id_especialidad'])) {
            $where[] = "e.id_especialidad = :especialidad";
            $params[':especialidad'] = $filters['id_especialidad'];
        }
        if (!empty($filters['status'])) {
            $where[] = "e.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $where[] = "(e.cedula LIKE :search OR e.first_name LIKE :search OR e.last_name LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql = "SELECT COUNT(e.id_estudiante) as total
                FROM estudiante e
                INNER JOIN trayecto t ON e.id_trayecto = t.id_trayecto
                LEFT JOIN especialidad es ON e.id_especialidad = es.id_especialidad";

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

    /**
     * Obtiene estudiantes filtrados con paginacion
     * Ordenados por apellido ASC, nombre ASC
     */
    public static function filter($filters = [], $limit = null, $offset = null)
    {
        $db = Database::getInstance();
        $where = [];
        $params = [];

        if (!empty($filters['id_trayecto'])) {
            $where[] = "e.id_trayecto = :trayecto";
            $params[':trayecto'] = $filters['id_trayecto'];
        }
        if (!empty($filters['id_especialidad'])) {
            $where[] = "e.id_especialidad = :especialidad";
            $params[':especialidad'] = $filters['id_especialidad'];
        }
        if (!empty($filters['status'])) {
            $where[] = "e.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $where[] = "(e.cedula LIKE :search OR e.first_name LIKE :search OR e.last_name LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql = self::$baseSelect;

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY e.last_name ASC, e.first_name ASC";

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

    /**
     * Busca un estudiante por ID
     */
    public static function findById($id)
    {
        $db = Database::getInstance();
        $query = self::$baseSelect . " WHERE e.id_estudiante = :id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Busca un estudiante por cedula
     */
    public static function findByCedula($cedula)
    {
        $db = Database::getInstance();
        $query = self::$baseSelect . " WHERE e.cedula = :cedula LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':cedula', $cedula);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene estadisticas para el dashboard
     * Retorna total, activos e inactivos
     */
    public static function getDashboardStats()
    {
        $db = Database::getInstance();
        $query = "SELECT
                    COUNT(e.id_estudiante) as total,
                    SUM(CASE WHEN e.status = 'activo' THEN 1 ELSE 0 END) as activos,
                    SUM(CASE WHEN e.status = 'inactivo' THEN 1 ELSE 0 END) as inactivos
                  FROM estudiante e";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
