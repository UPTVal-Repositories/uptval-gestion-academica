<?php

namespace Models;

use Core\Database;
use PDO;

class RolUser
{
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
}
