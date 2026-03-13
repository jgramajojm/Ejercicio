-- ============================================
-- EJERCICIO GIT - Hogares ISN
-- Script de base de datos
-- ============================================

CREATE DATABASE IF NOT EXISTS ejercicio_git CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE ejercicio_git;

CREATE TABLE IF NOT EXISTS empleados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    departamento VARCHAR(100) NOT NULL,
    salario DECIMAL(10,2) NOT NULL,
    fecha_ingreso DATE NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Datos de ejemplo para practicar
INSERT INTO empleados (nombre, apellido, email, departamento, salario, fecha_ingreso) VALUES
('Carlos', 'Mendoza', 'carlos.mendoza@hogaresisn.com', 'Desarrollo', 15000.00, '2023-01-15'),
('María', 'González', 'maria.gonzalez@hogaresisn.com', 'Ventas', 12000.00, '2022-06-01'),
('Luis', 'Ramírez', 'luis.ramirez@hogaresisn.com', 'Contabilidad', 13500.00, '2023-03-20'),
('Ana', 'López', 'ana.lopez@hogaresisn.com', 'Desarrollo', 14000.00, '2024-01-10');
