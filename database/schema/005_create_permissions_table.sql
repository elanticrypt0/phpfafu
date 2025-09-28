-- ============================================================================
-- Tabla: permissions
-- Descripción: Sistema de permisos granular
-- Versión: 1.0
-- Fecha: 2024-09-28
-- ============================================================================

CREATE TABLE IF NOT EXISTS permissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL COMMENT 'Nombre del permiso',
    display_name VARCHAR(100) NOT NULL COMMENT 'Nombre para mostrar',
    description TEXT NULL COMMENT 'Descripción del permiso',

    -- Categorización
    category VARCHAR(50) DEFAULT 'general' COMMENT 'Categoría del permiso',

    -- Control
    is_active BOOLEAN DEFAULT TRUE COMMENT 'Permiso activo',

    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de actualización'

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Permisos del sistema';

-- ============================================================================
-- Tabla: roles
-- Descripción: Roles de usuario
-- ============================================================================

CREATE TABLE IF NOT EXISTS roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL COMMENT 'Nombre del rol',
    display_name VARCHAR(100) NOT NULL COMMENT 'Nombre para mostrar',
    description TEXT NULL COMMENT 'Descripción del rol',

    -- Configuración
    is_default BOOLEAN DEFAULT FALSE COMMENT 'Rol por defecto para nuevos usuarios',
    is_active BOOLEAN DEFAULT TRUE COMMENT 'Rol activo',

    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de actualización'

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Roles del sistema';

-- ============================================================================
-- Tabla: role_permissions
-- Descripción: Relación muchos a muchos entre roles y permisos
-- ============================================================================

CREATE TABLE IF NOT EXISTS role_permissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id INT UNSIGNED NOT NULL COMMENT 'ID del rol',
    permission_id INT UNSIGNED NOT NULL COMMENT 'ID del permiso',

    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación',

    -- Claves foráneas
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE ON UPDATE CASCADE,

    -- Índice único para evitar duplicados
    UNIQUE KEY unique_role_permission (role_id, permission_id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Permisos asignados a roles';

-- ============================================================================
-- Tabla: user_roles
-- Descripción: Roles asignados a usuarios
-- ============================================================================

CREATE TABLE IF NOT EXISTS user_roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL COMMENT 'ID del usuario',
    role_id INT UNSIGNED NOT NULL COMMENT 'ID del rol',

    -- Control temporal
    expires_at TIMESTAMP NULL COMMENT 'Fecha de expiración del rol',
    is_active BOOLEAN DEFAULT TRUE COMMENT 'Asignación activa',

    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de actualización',

    -- Claves foráneas
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE ON UPDATE CASCADE,

    -- Índice único para evitar duplicados
    UNIQUE KEY unique_user_role (user_id, role_id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Roles asignados a usuarios';

-- ============================================================================
-- Tabla: user_permissions
-- Descripción: Permisos directos asignados a usuarios (excepciones)
-- ============================================================================

CREATE TABLE IF NOT EXISTS user_permissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL COMMENT 'ID del usuario',
    permission_id INT UNSIGNED NOT NULL COMMENT 'ID del permiso',

    -- Tipo de asignación
    type ENUM('grant', 'deny') DEFAULT 'grant' COMMENT 'Tipo: otorgar o denegar',

    -- Control temporal
    expires_at TIMESTAMP NULL COMMENT 'Fecha de expiración del permiso',
    is_active BOOLEAN DEFAULT TRUE COMMENT 'Asignación activa',

    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de actualización',

    -- Claves foráneas
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE ON UPDATE CASCADE,

    -- Índice único para evitar duplicados
    UNIQUE KEY unique_user_permission (user_id, permission_id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Permisos directos asignados a usuarios';

-- Índices adicionales
CREATE INDEX idx_permissions_category ON permissions(category);
CREATE INDEX idx_permissions_is_active ON permissions(is_active);
CREATE INDEX idx_roles_is_default ON roles(is_default);
CREATE INDEX idx_roles_is_active ON roles(is_active);
CREATE INDEX idx_user_roles_expires_at ON user_roles(expires_at);
CREATE INDEX idx_user_permissions_type ON user_permissions(type);
CREATE INDEX idx_user_permissions_expires_at ON user_permissions(expires_at);