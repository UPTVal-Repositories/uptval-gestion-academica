-- Migracion: Agregar tabla especialidad y campo id_especialidad a materia
-- Ejecutar en la base de datos MySQL

CREATE TABLE IF NOT EXISTS `especialidad` (
    `id_especialidad` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(120) NOT NULL,
    `status` VARCHAR(20) NOT NULL DEFAULT 'activo',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `materia` ADD COLUMN `id_especialidad` INT DEFAULT NULL AFTER `id_trayecto`;

ALTER TABLE `materia` ADD CONSTRAINT `fk_materia_especialidad`
    FOREIGN KEY (`id_especialidad`) REFERENCES `especialidad` (`id_especialidad`)
    ON DELETE SET NULL ON UPDATE CASCADE;
