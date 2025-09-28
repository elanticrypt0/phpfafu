-- ============================================================================
-- MIGRACIÓN: Poblar datos iniciales
-- Descripción: Inserta datos por defecto del sistema
-- Versión: 1.0
-- Fecha: 2024-09-28
-- ============================================================================

-- Ejecutar seeds en orden
SOURCE seeds/001_default_permissions.sql;
SOURCE seeds/002_default_roles.sql;
SOURCE seeds/003_default_users.sql;

-- Registrar esta migración
INSERT INTO migrations (migration, batch) VALUES
('002_seed_initial_data', 1);