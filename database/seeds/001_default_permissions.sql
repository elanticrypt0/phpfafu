-- ============================================================================
-- SEEDS: Permisos por defecto del sistema
-- Descripción: Inserta los permisos básicos del sistema
-- Fecha: 2024-09-28
-- ============================================================================

-- Permisos de usuarios
INSERT INTO permissions (name, display_name, description, category) VALUES
('users.view', 'Ver Usuarios', 'Puede ver la lista de usuarios', 'users'),
('users.create', 'Crear Usuarios', 'Puede crear nuevos usuarios', 'users'),
('users.edit', 'Editar Usuarios', 'Puede editar información de usuarios', 'users'),
('users.delete', 'Eliminar Usuarios', 'Puede eliminar usuarios del sistema', 'users'),
('users.manage', 'Gestionar Usuarios', 'Acceso completo a gestión de usuarios', 'users'),

-- Permisos de roles
('roles.view', 'Ver Roles', 'Puede ver la lista de roles', 'roles'),
('roles.create', 'Crear Roles', 'Puede crear nuevos roles', 'roles'),
('roles.edit', 'Editar Roles', 'Puede editar roles existentes', 'roles'),
('roles.delete', 'Eliminar Roles', 'Puede eliminar roles del sistema', 'roles'),
('roles.assign', 'Asignar Roles', 'Puede asignar roles a usuarios', 'roles'),

-- Permisos de contenido
('content.view', 'Ver Contenido', 'Puede ver contenido del sistema', 'content'),
('content.create', 'Crear Contenido', 'Puede crear nuevo contenido', 'content'),
('content.edit', 'Editar Contenido', 'Puede editar contenido existente', 'content'),
('content.delete', 'Eliminar Contenido', 'Puede eliminar contenido', 'content'),
('content.publish', 'Publicar Contenido', 'Puede publicar contenido', 'content'),

-- Permisos del sistema
('system.view', 'Ver Sistema', 'Puede ver información del sistema', 'system'),
('system.settings', 'Configuración', 'Puede modificar configuraciones del sistema', 'system'),
('system.logs', 'Ver Logs', 'Puede ver logs del sistema', 'system'),
('system.backup', 'Respaldos', 'Puede crear y gestionar respaldos', 'system'),
('system.maintenance', 'Mantenimiento', 'Puede poner el sistema en mantenimiento', 'system'),

-- Permisos de API
('api.access', 'Acceso a API', 'Puede acceder a la API del sistema', 'api'),
('api.tokens', 'Gestionar Tokens', 'Puede crear y gestionar tokens de API', 'api'),
('api.admin', 'API Admin', 'Acceso completo a funciones de API', 'api'),

-- Permisos de reportes
('reports.view', 'Ver Reportes', 'Puede ver reportes del sistema', 'reports'),
('reports.create', 'Crear Reportes', 'Puede crear nuevos reportes', 'reports'),
('reports.export', 'Exportar Reportes', 'Puede exportar reportes', 'reports'),

-- Permisos de base de datos
('database.view', 'Ver BD', 'Puede ver información de base de datos', 'database'),
('database.query', 'Ejecutar Queries', 'Puede ejecutar queries en la base de datos', 'database'),
('database.admin', 'Admin BD', 'Acceso completo a administración de BD', 'database');