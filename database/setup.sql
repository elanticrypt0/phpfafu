-- ============================================================================
-- SETUP COMPLETO DE LA BASE DE DATOS
-- Descripción: Script principal para configurar toda la base de datos
-- Versión: 1.0
-- Fecha: 2024-09-28
-- ============================================================================
--
-- Este archivo configura completamente la base de datos con:
-- - Todas las tablas necesarias
-- - Datos iniciales (permisos, roles, usuarios)
-- - Índices optimizados
-- - Usuarios de prueba
--
-- IMPORTANTE: Este script está diseñado para desarrollo/testing.
-- Para producción, revisar y cambiar las contraseñas por defecto.
--
-- USO:
-- mysql -u root -p nombre_de_bd < database/setup.sql
--
-- O desde el contenedor Docker:
-- docker compose exec db mysql -u root -p docker_php_api < database/setup.sql
-- ============================================================================

-- Configurar el charset y collation
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Deshabilitar verificaciones temporalmente para mejor rendimiento
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
SET AUTOCOMMIT = 0;

-- ============================================================================
-- CREAR TODAS LAS TABLAS
-- ============================================================================

-- Usuarios principales
SOURCE schema/001_create_users_table.sql;
SOURCE schema/002_create_user_profiles_table.sql;
SOURCE schema/003_create_user_sessions_table.sql;

-- Sistema de logs
SOURCE schema/004_create_activity_logs_table.sql;

-- Sistema de permisos
SOURCE schema/005_create_permissions_table.sql;

-- API y tokens
SOURCE schema/006_create_api_tokens_table.sql;

-- ============================================================================
-- TABLA DE CONTROL DE MIGRACIONES
-- ============================================================================

CREATE TABLE IF NOT EXISTS migrations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255) NOT NULL COMMENT 'Nombre de la migración',
    batch INT NOT NULL COMMENT 'Lote de migración',
    executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de ejecución'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Control de migraciones';

-- ============================================================================
-- POBLAR DATOS INICIALES
-- ============================================================================

-- Permisos del sistema
SOURCE seeds/001_default_permissions.sql;

-- Roles y asignación de permisos
SOURCE seeds/002_default_roles.sql;

-- Usuarios iniciales
SOURCE seeds/003_default_users.sql;

-- ============================================================================
-- REGISTRAR MIGRACIONES EJECUTADAS
-- ============================================================================

INSERT INTO migrations (migration, batch) VALUES
('001_create_users_table', 1),
('002_create_user_profiles_table', 1),
('003_create_user_sessions_table', 1),
('004_create_activity_logs_table', 1),
('005_create_permissions_table', 1),
('006_create_api_tokens_table', 1),
('007_default_permissions_seed', 1),
('008_default_roles_seed', 1),
('009_default_users_seed', 1);

-- ============================================================================
-- RESTAURAR CONFIGURACIONES
-- ============================================================================

-- Restaurar verificaciones
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;
COMMIT;
SET AUTOCOMMIT = 1;

-- ============================================================================
-- INFORMACIÓN DEL SETUP
-- ============================================================================

SELECT '============================================================================' as '';
SELECT 'SETUP COMPLETADO EXITOSAMENTE' as '';
SELECT '============================================================================' as '';
SELECT '' as '';
SELECT 'Tablas creadas:' as '';
SELECT '- users (usuarios principales)' as '';
SELECT '- user_profiles (perfiles extendidos)' as '';
SELECT '- user_sessions (sesiones)' as '';
SELECT '- activity_logs (logs de actividad)' as '';
SELECT '- permissions (permisos)' as '';
SELECT '- roles (roles del sistema)' as '';
SELECT '- role_permissions (permisos por rol)' as '';
SELECT '- user_roles (roles asignados)' as '';
SELECT '- user_permissions (permisos directos)' as '';
SELECT '- api_tokens (tokens de API)' as '';
SELECT '- api_requests (logs de API)' as '';
SELECT '- migrations (control de migraciones)' as '';
SELECT '' as '';
SELECT 'Usuarios creados para testing:' as '';
SELECT '- superadmin / password (Super Administrador)' as '';
SELECT '- admin / password (Administrador)' as '';
SELECT '- moderator / password (Moderador)' as '';
SELECT '- demo / demo (Usuario Demo)' as '';
SELECT '- testuser / test123 (Usuario de Prueba)' as '';
SELECT '' as '';
SELECT 'IMPORTANTE: Cambiar contraseñas en producción!' as '';
SELECT '============================================================================' as '';

-- Mostrar estadísticas
SELECT
    'Estadísticas del Setup:' as '',
    (SELECT COUNT(*) FROM users) as 'Usuarios creados',
    (SELECT COUNT(*) FROM roles) as 'Roles creados',
    (SELECT COUNT(*) FROM permissions) as 'Permisos creados',
    (SELECT COUNT(*) FROM role_permissions) as 'Permisos asignados',
    (SELECT COUNT(*) FROM migrations) as 'Migraciones registradas';