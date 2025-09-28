-- ============================================================================
-- SEEDS: Roles por defecto del sistema
-- Descripción: Inserta los roles básicos y sus permisos
-- Fecha: 2024-09-28
-- ============================================================================

-- Insertar roles por defecto
INSERT INTO roles (name, display_name, description, is_default, is_active) VALUES
('super_admin', 'Super Administrador', 'Acceso completo a todas las funciones del sistema', FALSE, TRUE),
('admin', 'Administrador', 'Administrador con acceso a la mayoría de funciones', FALSE, TRUE),
('moderator', 'Moderador', 'Moderador con permisos limitados de administración', FALSE, TRUE),
('user', 'Usuario', 'Usuario estándar del sistema', TRUE, TRUE),
('guest', 'Invitado', 'Usuario con permisos muy limitados', FALSE, TRUE);

-- Asignar TODOS los permisos al Super Administrador
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
CROSS JOIN permissions p
WHERE r.name = 'super_admin';

-- Asignar permisos al Administrador (todos excepto algunos críticos del sistema)
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
CROSS JOIN permissions p
WHERE r.name = 'admin'
AND p.name NOT IN ('system.maintenance', 'database.admin');

-- Asignar permisos al Moderador
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
CROSS JOIN permissions p
WHERE r.name = 'moderator'
AND p.name IN (
    'users.view', 'users.edit',
    'content.view', 'content.create', 'content.edit', 'content.publish',
    'reports.view',
    'api.access'
);

-- Asignar permisos al Usuario estándar
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
CROSS JOIN permissions p
WHERE r.name = 'user'
AND p.name IN (
    'content.view', 'content.create',
    'api.access'
);

-- Asignar permisos mínimos al Invitado
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
CROSS JOIN permissions p
WHERE r.name = 'guest'
AND p.name IN (
    'content.view'
);