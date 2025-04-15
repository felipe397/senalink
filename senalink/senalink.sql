
-- Creación de la base de datos
CREATE DATABASE IF NOT EXISTS senalink;
USE senalink;

-- Tabla de Usuarios (Super Administrador y Administradores)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_documento ENUM('CC', 'TI', 'CE', 'Pasaporte') NOT NULL,
    numero_documento VARCHAR(20) UNIQUE NOT NULL,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    nickname VARCHAR(50) UNIQUE NOT NULL,
    correo VARCHAR(100) UNIQUE NOT NULL,
    contrasena VARCHAR(255) NOT NULL,
    rol ENUM('SuperAdmin', 'AdminSENA') NOT NULL,
    estado ENUM('Activo', 'Suspendido', 'Desactivado') DEFAULT 'Activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de Empresas
CREATE TABLE empresas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nit VARCHAR(20) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(15) NOT NULL,
    correo VARCHAR(100) UNIQUE NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    actividad_economica VARCHAR(255) NOT NULL,
    contrasena VARCHAR(255) NOT NULL,
    estado ENUM('Activo', 'Suspendido', 'Desactivado') DEFAULT 'Activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de Programas de Formación
CREATE TABLE programas_formacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    duracion_meses INT NOT NULL,
    nivel ENUM('Tecnólogo', 'Técnico', 'Profundización Técnica', 'Operario', 'Especialización') NOT NULL,
    estado ENUM('Activo', 'Suspendido', 'Desactivado') DEFAULT 'Activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de Diagnósticos Empresariales
CREATE TABLE diagnosticos_empresariales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    resultado TEXT NOT NULL,
    fecha_realizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE
);

-- Tabla de Reportes de Usuarios
CREATE TABLE reportes_usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    generado_por INT NOT NULL,
    fecha_generacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    formato ENUM('PDF', 'XML') NOT NULL,
    FOREIGN KEY (generado_por) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabla de Reportes de Empresas
CREATE TABLE reportes_empresas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    generado_por INT NOT NULL,
    fecha_generacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    formato ENUM('PDF', 'XML') NOT NULL,
    FOREIGN KEY (generado_por) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabla de Reportes de Diagnósticos Empresariales
CREATE TABLE reportes_diagnosticos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    fecha_generacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    formato ENUM('PDF', 'XML') NOT NULL,
    FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE
);

-- Tabla de Reportes de Programas de Formación
CREATE TABLE reportes_programas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    generado_por INT NOT NULL,
    fecha_generacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    formato ENUM('PDF', 'XML') NOT NULL,
    FOREIGN KEY (generado_por) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabla de Recuperación de Contraseñas
CREATE TABLE recuperacion_contrasenas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    codigo_verificacion VARCHAR(6) NOT NULL,
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Procedimiento para Validación de Inicio de Sesión
DELIMITER //
CREATE PROCEDURE validar_login(IN correo_o_nit VARCHAR(100), IN password VARCHAR(255))
BEGIN
    IF EXISTS (SELECT * FROM usuarios WHERE correo = correo_o_nit AND contrasena = password) THEN
        SELECT 'Usuario válido' AS mensaje;
    ELSEIF EXISTS (SELECT * FROM empresas WHERE nit = correo_o_nit AND contrasena = password) THEN
        SELECT 'Empresa válida' AS mensaje;
    ELSE
        SELECT 'Credenciales incorrectas' AS mensaje;
    END IF;
END //
DELIMITER ;
