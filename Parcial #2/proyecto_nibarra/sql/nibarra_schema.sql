-- nibarra_schema.sql
CREATE DATABASE IF NOT EXISTS nibarra CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nibarra;

-- usuarios para el acceso (simple)
CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  username VARCHAR(80) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  rol VARCHAR(30) DEFAULT 'tecnico',
  creado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- tabla principal de equipos/servicios
CREATE TABLE IF NOT EXISTS equipos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ingreso_fecha DATE NOT NULL,
  salida_fecha DATE NULL,
  equipo VARCHAR(150) NOT NULL,
  marca VARCHAR(100) NULL,
  serie VARCHAR(100) NULL,
  tipo_servicio VARCHAR(50) NOT NULL, -- e.g. mantenimiento, reparacion
  tipo_mantenimiento ENUM('predictivo','preventivo','correctivo') DEFAULT 'preventivo',
  estado ENUM('por hacer','espera material','en revision','terminada') DEFAULT 'por hacer',
  costo_inicial DECIMAL(10,2) DEFAULT 0.00,
  costo_final DECIMAL(10,2) DEFAULT 0.00,
  observacion TEXT,
  progreso TINYINT DEFAULT 0, -- 0..100
  creado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  actualizado_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
);

-- ejemplo usuario (password: docente123) -> reemplazar en producción
INSERT INTO usuarios (nombre, username, password_hash, rol)
VALUES ('Profesor', 'profesor', SHA2('docente123',256), 'admin');
