-- ============================================================================
-- Tabla: user_sessions
-- Descripción: Gestión de sesiones de usuario
-- Versión: 1.0
-- Fecha: 2024-09-28
-- ============================================================================

CREATE TABLE IF NOT EXISTS user_sessions (
    id VARCHAR(128) PRIMARY KEY COMMENT 'ID único de la sesión',
    user_id INT UNSIGNED NULL COMMENT 'ID del usuario (NULL para sesiones anónimas)',
    ip_address VARCHAR(45) NOT NULL COMMENT 'Dirección IP',
    user_agent TEXT NULL COMMENT 'User Agent del navegador',

    -- Datos de la sesión
    payload LONGTEXT NOT NULL COMMENT 'Datos serializados de la sesión',
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Última actividad',

    -- Información del dispositivo/navegador
    device_type ENUM('desktop', 'mobile', 'tablet', 'unknown') DEFAULT 'unknown' COMMENT 'Tipo de dispositivo',
    browser VARCHAR(50) NULL COMMENT 'Navegador',
    platform VARCHAR(50) NULL COMMENT 'Sistema operativo',

    -- Geolocalización (opcional)
    country VARCHAR(2) NULL COMMENT 'Código de país',
    city VARCHAR(100) NULL COMMENT 'Ciudad',

    -- Control de sesión
    is_active BOOLEAN DEFAULT TRUE COMMENT 'Sesión activa',
    expires_at TIMESTAMP NULL COMMENT 'Fecha de expiración',

    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación',

    -- Claves foráneas
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sesiones de usuario';

-- Índices para optimizar consultas
CREATE INDEX idx_user_sessions_user_id ON user_sessions(user_id);
CREATE INDEX idx_user_sessions_last_activity ON user_sessions(last_activity);
CREATE INDEX idx_user_sessions_ip_address ON user_sessions(ip_address);
CREATE INDEX idx_user_sessions_expires_at ON user_sessions(expires_at);
CREATE INDEX idx_user_sessions_is_active ON user_sessions(is_active);