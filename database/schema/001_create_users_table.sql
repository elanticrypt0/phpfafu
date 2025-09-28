-- ============================================================================
-- Tabla: users
-- Descripción: Almacena información de usuarios del sistema
-- Versión: 1.0
-- Fecha: 2024-09-28
-- ============================================================================

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL COMMENT 'Nombre de usuario único',
    email VARCHAR(100) UNIQUE NOT NULL COMMENT 'Correo electrónico único',
    email_verified_at TIMESTAMP NULL COMMENT 'Fecha de verificación del email',
    password_hash VARCHAR(255) NOT NULL COMMENT 'Hash de la contraseña',

    -- Información personal
    first_name VARCHAR(50) NULL COMMENT 'Nombre',
    last_name VARCHAR(50) NULL COMMENT 'Apellido',
    phone VARCHAR(20) NULL COMMENT 'Teléfono',
    avatar VARCHAR(255) NULL COMMENT 'URL del avatar',

    -- Configuración de cuenta
    role ENUM('admin', 'moderator', 'user') DEFAULT 'user' COMMENT 'Rol del usuario',
    status ENUM('active', 'inactive', 'suspended', 'pending') DEFAULT 'active' COMMENT 'Estado de la cuenta',
    language VARCHAR(5) DEFAULT 'es' COMMENT 'Idioma preferido',
    timezone VARCHAR(50) DEFAULT 'America/Mexico_City' COMMENT 'Zona horaria',

    -- Seguridad
    two_factor_enabled BOOLEAN DEFAULT FALSE COMMENT 'Autenticación de dos factores habilitada',
    two_factor_secret VARCHAR(32) NULL COMMENT 'Secreto para 2FA',
    remember_token VARCHAR(100) NULL COMMENT 'Token para recordar sesión',
    password_reset_token VARCHAR(100) NULL COMMENT 'Token para resetear contraseña',
    password_reset_expires TIMESTAMP NULL COMMENT 'Expiración del token de reset',

    -- Metadata
    last_login_at TIMESTAMP NULL COMMENT 'Última fecha de login',
    last_login_ip VARCHAR(45) NULL COMMENT 'Última IP de login',
    login_attempts INT DEFAULT 0 COMMENT 'Intentos de login fallidos',
    locked_until TIMESTAMP NULL COMMENT 'Cuenta bloqueada hasta',

    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de actualización',
    deleted_at TIMESTAMP NULL COMMENT 'Fecha de eliminación lógica'

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Usuarios del sistema';

-- Índices para optimizar consultas
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_status ON users(status);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_users_created_at ON users(created_at);
CREATE INDEX idx_users_last_login_at ON users(last_login_at);
CREATE INDEX idx_users_deleted_at ON users(deleted_at);