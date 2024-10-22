-- Base de Datos Cato_Chan
DROP DATABASE IF EXISTS cato_Chan;
	-- Creamos base de datos 'empresa'
CREATE DATABASE cato_Chan;
	-- Designamos 'empresa' como base de datos actual, a la que se hará referencia en el resto del código
USE cato_Chan;

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    correo VARCHAR(100) UNIQUE NOT NULL,
    dni VARBINARY(128) UNIQUE NOT NULL, -- DNI cifrado para mayor seguridad
    password VARCHAR(255) NOT NULL, -- Contraseña cifrada (por ejemplo, bcrypt)
    foto VARCHAR(255) DEFAULT 'default.png',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Índice único para correo (por eficiencia en búsquedas frecuentes)
CREATE UNIQUE INDEX idx_correo ON usuarios (correo);

-- Tabla de tipos de post
CREATE TABLE tipos_post (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(50) UNIQUE NOT NULL
);

-- Inserción de tipos de post predefinidos
INSERT INTO tipos_post (tipo) VALUES ('CATO'), ('CHAN');

-- Tabla de posts
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL, -- Relación con usuarios
    contenido TEXT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tipo_id INT NOT NULL, -- Relación con tipos de post
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (tipo_id) REFERENCES tipos_post(id) ON DELETE RESTRICT,
    INDEX idx_usuario_post (usuario_id) -- Mejora las consultas por usuario
);

-- Tabla de comentarios
CREATE TABLE comentarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL, -- Relación con post
    usuario_id INT NOT NULL, -- Relación con usuario
    contenido TEXT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    anonimo BOOLEAN DEFAULT 0, -- Si el comentario es anónimo o no
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_post_comentario (post_id), -- Optimiza consultas por post
    INDEX idx_usuario_comentario (usuario_id) -- Optimiza consultas por usuario
);
