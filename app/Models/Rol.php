<?php

namespace Models;
use PDO;
use Core\Database;

class Rol{

    public static function all() {
        $db = Database::getInstance();
        $query = "SELECT id_rol, name, status
                  FROM rol
                  WHERE status = 'activo'
                  ORDER BY name ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAll() {
        $db = Database::getInstance();
        $query = "SELECT id_rol, name, status
                  FROM rol
                  ORDER BY name ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById($idRol) {
        $db = Database::getInstance();
        $query = "SELECT id_rol, name, status
                  FROM rol
                  WHERE id_rol = :id";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id', (int) $idRol, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
