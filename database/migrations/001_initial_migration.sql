-- ============================================================================
-- MIGRACIÓN INICIAL: Crear todas las tablas del sistema
-- Descripción: Ejecuta la creación de todas las tablas necesarias
-- Versión: 1.0
-- Fecha: 2024-09-28
-- ============================================================================

-- Ejecutar schemas en orden
SOURCE schema/001_create_users_table.sql;
SOURCE schema/002_create_user_profiles_table.sql;
SOURCE schema/003_create_user_sessions_table.sql;
SOURCE schema/004_create_activity_logs_table.sql;
SOURCE schema/005_create_permissions_table.sql;
SOURCE schema/006_create_api_tokens_table.sql;

-- Tabla para control de migraciones
CREATE TABLE IF NOT EXISTS migrations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255) NOT NULL COMMENT 'Nombre de la migración',
    batch INT NOT NULL COMMENT 'Lote de migración',
    executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de ejecución'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Control de migraciones';

-- Registrar esta migración
INSERT INTO migrations (migration, batch) VALUES
('001_initial_migration', 1);