-- ============================================================================
-- Tabla: activity_logs
-- Descripción: Registro de actividades del sistema
-- Versión: 1.0
-- Fecha: 2024-09-28
-- ============================================================================

CREATE TABLE IF NOT EXISTS activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL COMMENT 'ID del usuario (NULL para acciones del sistema)',

    -- Información de la actividad
    action VARCHAR(100) NOT NULL COMMENT 'Acción realizada',
    description TEXT NULL COMMENT 'Descripción detallada de la actividad',

    -- Contexto de la actividad
    subject_type VARCHAR(100) NULL COMMENT 'Tipo de entidad afectada (ej: User, Post)',
    subject_id INT UNSIGNED NULL COMMENT 'ID de la entidad afectada',

    -- Metadata
    properties JSON NULL COMMENT 'Propiedades adicionales en formato JSON',

    -- Información de la request
    ip_address VARCHAR(45) NULL COMMENT 'Dirección IP',
    user_agent TEXT NULL COMMENT 'User Agent',

    -- Categorización
    log_level ENUM('emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug') DEFAULT 'info' COMMENT 'Nivel de log',
    category VARCHAR(50) DEFAULT 'general' COMMENT 'Categoría de la actividad',

    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación',

    -- Claves foráneas
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro de actividades del sistema';

-- Índices para optimizar consultas
CREATE INDEX idx_activity_logs_user_id ON activity_logs(user_id);
CREATE INDEX idx_activity_logs_action ON activity_logs(action);
CREATE INDEX idx_activity_logs_subject ON activity_logs(subject_type, subject_id);
CREATE INDEX idx_activity_logs_log_level ON activity_logs(log_level);
CREATE INDEX idx_activity_logs_category ON activity_logs(category);
CREATE INDEX idx_activity_logs_created_at ON activity_logs(created_at);
CREATE INDEX idx_activity_logs_ip_address ON activity_logs(ip_address);