-- =====================================================
-- Migracion: Tabla estudiante
-- Sistema de Gestion Academica UPTVal
-- =====================================================

CREATE TABLE IF NOT EXISTS `estudiante` (
    `id_estudiante`   INT AUTO_INCREMENT PRIMARY KEY,
    `cedula`          VARCHAR(20) NOT NULL UNIQUE,
    `first_name`      VARCHAR(120) NOT NULL,
    `last_name`       VARCHAR(120) NOT NULL,
    `sex`             VARCHAR(10) DEFAULT NULL,
    `birth_date`      DATE DEFAULT NULL,
    `phone`           VARCHAR(20) DEFAULT NULL,
    `email`           VARCHAR(120) DEFAULT NULL,
    `seccion`         VARCHAR(10) DEFAULT NULL,
    `id_trayecto`     INT NOT NULL,
    `id_especialidad` INT DEFAULT NULL,
    `status`          VARCHAR(20) NOT NULL DEFAULT 'activo',
    `created_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_estudiante_trayecto`
        FOREIGN KEY (`id_trayecto`) REFERENCES `trayecto` (`id_trayecto`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_estudiante_especialidad`
        FOREIGN KEY (`id_especialidad`) REFERENCES `especialidad` (`id_especialidad`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Datos de ejemplo (15 estudiantes)
-- Distribuidos entre trayectos y especialidades
-- Ordenados variadamente por apellido para verificar ORDER BY
-- =====================================================

INSERT INTO `estudiante` (`cedula`, `first_name`, `last_name`, `sex`, `birth_date`, `phone`, `email`, `seccion`, `id_trayecto`, `id_especialidad`, `status`) VALUES
-- Trayecto Inicial (id=5) - sin especialidad aun
('V-28456123', 'Maria', 'Acosta Rodriguez', 'F', '2005-03-15', '0414-5551234', 'maria.acosta@estudiante.uptval.edu.ve', 'A', 5, NULL, 'activo'),
('V-29123456', 'Carlos', 'Bermudez Silva', 'M', '2004-08-22', '0424-5552345', 'carlos.bermudez@estudiante.uptval.edu.ve', 'A', 5, NULL, 'activo'),
('V-28789012', 'Ana', 'Castro Mendez', 'F', '2005-01-10', '0412-5553456', 'ana.castro@estudiante.uptval.edu.ve', 'B', 5, NULL, 'activo'),

-- Trayecto I (id=1)
('V-27654321', 'Luis', 'Diaz Fernandez', 'M', '2003-11-05', '0414-5554567', 'luis.diaz@estudiante.uptval.edu.ve', 'A', 1, 1, 'activo'),
('V-27890123', 'Carmen', 'Espinoza Torres', 'F', '2003-06-18', '0424-5555678', 'carmen.espinoza@estudiante.uptval.edu.ve', 'B', 1, 2, 'activo'),
('V-28234567', 'Pedro', 'Fuentes Garcia', 'M', '2004-02-28', '0412-5556789', 'pedro.fuentes@estudiante.uptval.edu.ve', 'A', 1, 1, 'inactivo'),

-- Trayecto II (id=2)
('V-26543210', 'Rosa', 'Gonzalez Herrera', 'F', '2002-09-12', '0414-5557890', 'rosa.gonzalez@estudiante.uptval.edu.ve', 'A', 2, 3, 'activo'),
('V-26789012', 'Miguel', 'Hernandez Lopez', 'M', '2002-04-25', '0424-5558901', 'miguel.hernandez@estudiante.uptval.edu.ve', 'B', 2, 4, 'activo'),
('V-27012345', 'Sofia', 'Jimenez Ramos', 'F', '2003-07-08', '0412-5559012', 'sofia.jimenez@estudiante.uptval.edu.ve', 'A', 2, 5, 'activo'),

-- Trayecto III (id=3)
('V-25678901', 'Andres', 'Lopez Martinez', 'M', '2001-12-03', '0414-5550123', 'andres.lopez@estudiante.uptval.edu.ve', 'A', 3, 1, 'activo'),
('V-25890123', 'Elena', 'Martinez Nunez', 'F', '2001-05-17', '0424-5551235', 'elena.martinez@estudiante.uptval.edu.ve', 'B', 3, 2, 'inactivo'),
('V-26012345', 'Jorge', 'Navarro Ortiz', 'M', '2002-10-20', '0412-5552346', 'jorge.navarro@estudiante.uptval.edu.ve', 'A', 3, 3, 'activo'),

-- Trayecto IV (id=4)
('V-24567890', 'Patricia', 'Perez Quintero', 'F', '2000-08-14', '0414-5553457', 'patricia.perez@estudiante.uptval.edu.ve', 'A', 4, 1, 'activo'),
('V-24789012', 'Roberto', 'Ramirez Sanchez', 'M', '2000-03-27', '0424-5554568', 'roberto.ramirez@estudiante.uptval.edu.ve', 'B', 4, 4, 'activo'),
('V-25012345', 'Lucia', 'Zambrano Vargas', 'F', '2001-01-09', '0412-5555679', 'lucia.zambrano@estudiante.uptval.edu.ve', 'A', 4, 5, 'inactivo');
