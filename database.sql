-- =====================================================================
-- Clínica Dental - Historia clínica odontológica
-- Importar en la base de datos YA CREADA (phpMyAdmin > Importar).
-- En el hosting la base de datos se crea desde el panel, por eso
-- este script no incluye CREATE DATABASE ni USE.
-- =====================================================================

-- Usuarios que pueden iniciar sesión
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(60) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    clave VARCHAR(255) NOT NULL            -- hash generado con password_hash()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pacientes de la clínica
CREATE TABLE IF NOT EXISTS pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cedula VARCHAR(12) NOT NULL UNIQUE,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    sexo CHAR(1) NOT NULL,                 -- M o F
    telefono VARCHAR(20) NOT NULL DEFAULT '',
    correo VARCHAR(100) NOT NULL DEFAULT '',
    direccion VARCHAR(200) NOT NULL DEFAULT '',
    antecedentes VARCHAR(1000) NOT NULL DEFAULT '',   -- alergias, enfermedades, medicamentos
    creado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Catálogo de procedimientos que ofrece el odontólogo
CREATE TABLE IF NOT EXISTS procedimientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL UNIQUE,
    descripcion VARCHAR(255) NOT NULL DEFAULT '',
    precio DECIMAL(10,2) NOT NULL DEFAULT 0,
    activo TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Historia clínica: cada fila es un procedimiento realizado a un paciente
CREATE TABLE IF NOT EXISTS atenciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    procedimiento_id INT NOT NULL,
    fecha DATE NOT NULL,
    pieza_dental VARCHAR(20) NOT NULL DEFAULT '',
    diagnostico VARCHAR(500) NOT NULL DEFAULT '',
    observaciones VARCHAR(1000) NOT NULL DEFAULT '',
    costo DECIMAL(10,2) NOT NULL DEFAULT 0,
    creado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    -- Si se borra el paciente, se borra su historia
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
    -- No se puede borrar un procedimiento que ya fue usado en una historia
    FOREIGN KEY (procedimiento_id) REFERENCES procedimientos(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Datos iniciales
-- Usuario de acceso:  admin@clinica.com  /  Admin123   (cámbiela luego)
-- ---------------------------------------------------------------------
INSERT INTO usuarios (nombre, correo, clave) VALUES
('Administrador', 'admin@clinica.com', '$2y$12$6eY4.Rs./6Hk1LmnQcP5He0gD647ZAnALhBvftAr.yyToskTXed4G');

INSERT INTO procedimientos (nombre, descripcion, precio, activo) VALUES
('Limpieza dental', 'Profilaxis y remoción de sarro', 30.00, 1),
('Blanqueamiento dental', 'Aclarado de la dentadura con gel blanqueador', 120.00, 1),
('Extracción de muela', 'Extracción simple de pieza dental', 40.00, 1),
('Extracción de cordal', 'Extracción de tercer molar (muela del juicio)', 80.00, 1),
('Tratamiento de conductos', 'Endodoncia de la pieza afectada', 150.00, 1),
('Calza de muela', 'Obturación con resina de una caries', 50.00, 1);

INSERT INTO pacientes (cedula, nombre, apellido, fecha_nacimiento, sexo, telefono, correo, direccion, antecedentes) VALUES
('12345678', 'María', 'Gómez', '1990-05-14', 'F', '0414-1234567', 'maria.gomez@example.com', 'Av. 5 de Julio, Maracaibo', 'Alérgica a la penicilina'),
('23456789', 'Carlos', 'Mendoza', '1985-11-02', 'M', '0424-7654321', 'carlos.mendoza@example.com', 'Calle 72, Maracaibo', 'Hipertensión controlada');

INSERT INTO atenciones (paciente_id, procedimiento_id, fecha, pieza_dental, diagnostico, observaciones, costo) VALUES
(1, 1, '2026-09-10', '', 'Acumulación de sarro', 'Se recomienda control en 6 meses', 30.00),
(1, 6, '2026-10-08', '36', 'Caries en molar inferior izquierdo', 'Calza con resina sin complicaciones', 50.00),
(2, 4, '2026-10-20', '48', 'Cordal semi-retenido con dolor', 'Se indicó analgésico y reposo', 80.00);
