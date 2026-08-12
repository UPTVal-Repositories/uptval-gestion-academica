-- Migracion: Agregar tabla aula (aulas y laboratorios)
-- Ejecutar en la base de datos MySQL

CREATE TABLE IF NOT EXISTS `aula` (
    `id_aula` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(20) NOT NULL UNIQUE,
    `name` VARCHAR(120) NOT NULL,
    `type` VARCHAR(20) NOT NULL DEFAULT 'aula',
    `id_department` INT NOT NULL,
    `status` VARCHAR(20) NOT NULL DEFAULT 'activo',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_aula_department`
        FOREIGN KEY (`id_department`) REFERENCES `department` (`id_department`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
