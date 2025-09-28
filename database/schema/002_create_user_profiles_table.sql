-- ============================================================================
-- Tabla: user_profiles
-- Descripción: Información extendida de perfiles de usuario
-- Versión: 1.0
-- Fecha: 2024-09-28
-- ============================================================================

CREATE TABLE IF NOT EXISTS user_profiles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL COMMENT 'ID del usuario',

    -- Información personal extendida
    bio TEXT NULL COMMENT 'Biografía del usuario',
    website VARCHAR(255) NULL COMMENT 'Sitio web personal',
    location VARCHAR(100) NULL COMMENT 'Ubicación',
    birth_date DATE NULL COMMENT 'Fecha de nacimiento',
    gender ENUM('male', 'female', 'other', 'prefer_not_to_say') NULL COMMENT 'Género',

    -- Redes sociales
    social_facebook VARCHAR(100) NULL COMMENT 'Usuario de Facebook',
    social_twitter VARCHAR(100) NULL COMMENT 'Usuario de Twitter',
    social_linkedin VARCHAR(100) NULL COMMENT 'Usuario de LinkedIn',
    social_instagram VARCHAR(100) NULL COMMENT 'Usuario de Instagram',
    social_github VARCHAR(100) NULL COMMENT 'Usuario de GitHub',

    -- Configuraciones de privacidad
    profile_visibility ENUM('public', 'private', 'friends') DEFAULT 'public' COMMENT 'Visibilidad del perfil',
    show_email BOOLEAN DEFAULT FALSE COMMENT 'Mostrar email públicamente',
    show_phone BOOLEAN DEFAULT FALSE COMMENT 'Mostrar teléfono públicamente',
    allow_messages BOOLEAN DEFAULT TRUE COMMENT 'Permitir mensajes de otros usuarios',

    -- Configuraciones de notificaciones
    notify_email_messages BOOLEAN DEFAULT TRUE COMMENT 'Notificar mensajes por email',
    notify_email_updates BOOLEAN DEFAULT TRUE COMMENT 'Notificar actualizaciones por email',
    notify_email_marketing BOOLEAN DEFAULT FALSE COMMENT 'Recibir emails de marketing',

    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de actualización',

    -- Claves foráneas
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Perfiles extendidos de usuarios';

-- Índices
CREATE UNIQUE INDEX idx_user_profiles_user_id ON user_profiles(user_id);
CREATE INDEX idx_user_profiles_visibility ON user_profiles(profile_visibility);
CREATE INDEX idx_user_profiles_location ON user_profiles(location);