-- ============================================================================
-- SEEDS: Usuarios por defecto del sistema
-- Descripción: Inserta usuarios iniciales para testing y administración
-- Fecha: 2024-09-28
-- IMPORTANTE: Cambiar contraseñas en producción
-- ============================================================================

-- Usuario Super Administrador
INSERT INTO users (
    username, email, email_verified_at, password_hash,
    first_name, last_name, role, status, language, timezone,
    two_factor_enabled, created_at, updated_at
) VALUES (
    'superadmin',
    'superadmin@localhost.com',
    NOW(),
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    'Super',
    'Admin',
    'admin',
    'active',
    'es',
    'America/Mexico_City',
    FALSE,
    NOW(),
    NOW()
);

-- Usuario Administrador
INSERT INTO users (
    username, email, email_verified_at, password_hash,
    first_name, last_name, role, status, language, timezone,
    created_at, updated_at
) VALUES (
    'admin',
    'admin@localhost.com',
    NOW(),
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    'Administrador',
    'Sistema',
    'admin',
    'active',
    'es',
    'America/Mexico_City',
    NOW(),
    NOW()
);

-- Usuario Demo (para testing)
INSERT INTO users (
    username, email, email_verified_at, password_hash,
    first_name, last_name, role, status, language, timezone,
    created_at, updated_at
) VALUES (
    'demo',
    'demo@localhost.com',
    NOW(),
    '$2y$10$AilKdpgZhZmm8KPYs3Q/TO7MkLp4e8LggRv6.c3F72VTfqy6.XRGG', -- password: demo
    'Usuario',
    'Demo',
    'user',
    'active',
    'es',
    'America/Mexico_City',
    NOW(),
    NOW()
);

-- Usuario Moderador
INSERT INTO users (
    username, email, email_verified_at, password_hash,
    first_name, last_name, role, status, language, timezone,
    created_at, updated_at
) VALUES (
    'moderator',
    'moderator@localhost.com',
    NOW(),
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    'Usuario',
    'Moderador',
    'moderator',
    'active',
    'es',
    'America/Mexico_City',
    NOW(),
    NOW()
);

-- Usuario Test (para desarrollo)
INSERT INTO users (
    username, email, email_verified_at, password_hash,
    first_name, last_name, role, status, language, timezone,
    created_at, updated_at
) VALUES (
    'testuser',
    'test@localhost.com',
    NOW(),
    '$2y$10$1rw4OqP/WIGGzz4/GNwlYOuFJhEp.Nh9.KfqNjz8ZnLKp8SYJ5kNi', -- password: test123
    'Usuario',
    'Prueba',
    'user',
    'active',
    'es',
    'America/Mexico_City',
    NOW(),
    NOW()
);

-- Asignar roles a los usuarios
-- Super Admin
INSERT INTO user_roles (user_id, role_id, is_active, created_at, updated_at)
SELECT u.id, r.id, TRUE, NOW(), NOW()
FROM users u, roles r
WHERE u.username = 'superadmin' AND r.name = 'super_admin';

-- Admin
INSERT INTO user_roles (user_id, role_id, is_active, created_at, updated_at)
SELECT u.id, r.id, TRUE, NOW(), NOW()
FROM users u, roles r
WHERE u.username = 'admin' AND r.name = 'admin';

-- Moderator
INSERT INTO user_roles (user_id, role_id, is_active, created_at, updated_at)
SELECT u.id, r.id, TRUE, NOW(), NOW()
FROM users u, roles r
WHERE u.username = 'moderator' AND r.name = 'moderator';

-- Demo y Test users (rol de usuario estándar)
INSERT INTO user_roles (user_id, role_id, is_active, created_at, updated_at)
SELECT u.id, r.id, TRUE, NOW(), NOW()
FROM users u, roles r
WHERE u.username IN ('demo', 'testuser') AND r.name = 'user';

-- Crear perfiles básicos para los usuarios
INSERT INTO user_profiles (
    user_id, bio, profile_visibility, show_email, show_phone,
    allow_messages, notify_email_messages, notify_email_updates,
    notify_email_marketing, created_at, updated_at
)
SELECT
    id,
    CASE
        WHEN username = 'superadmin' THEN 'Super Administrador del sistema con acceso completo'
        WHEN username = 'admin' THEN 'Administrador del sistema'
        WHEN username = 'moderator' THEN 'Moderador con permisos de gestión de contenido'
        WHEN username = 'demo' THEN 'Usuario de demostración para testing'
        WHEN username = 'testuser' THEN 'Usuario de prueba para desarrollo'
        ELSE 'Usuario del sistema'
    END,
    'public',
    FALSE,
    FALSE,
    TRUE,
    TRUE,
    TRUE,
    FALSE,
    NOW(),
    NOW()
FROM users;