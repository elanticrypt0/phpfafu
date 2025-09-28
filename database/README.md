# Base de Datos - Docker PHP API

Este directorio contiene todos los archivos SQL necesarios para configurar la base de datos de la aplicación.

## Estructura

```
database/
├── schema/          # Definiciones de tablas
├── seeds/           # Datos iniciales
├── migrations/      # Scripts de migración
├── setup.sql        # Setup completo
└── README.md        # Este archivo
```

## Uso Rápido

### Setup Completo (Recomendado)

```bash
# Desde el directorio raíz del proyecto
docker compose exec db mysql -u root -p docker_php_api < database/setup.sql
```

### Setup Manual (paso a paso)

```bash
# 1. Crear tablas
docker compose exec db mysql -u root -p docker_php_api < database/migrations/001_initial_migration.sql

# 2. Poblar datos iniciales
docker compose exec db mysql -u root -p docker_php_api < database/migrations/002_seed_initial_data.sql
```

## Archivos de Schema

### `001_create_users_table.sql`
Tabla principal de usuarios con:
- Información básica (username, email, password)
- Campos de seguridad (2FA, tokens, bloqueos)
- Metadatos (último login, IP, etc.)
- Soft deletes

### `002_create_user_profiles_table.sql`
Perfiles extendidos con:
- Información personal adicional
- Redes sociales
- Configuraciones de privacidad
- Preferencias de notificaciones

### `003_create_user_sessions_table.sql`
Gestión de sesiones:
- Sesiones activas de usuarios
- Información del dispositivo/navegador
- Geolocalización opcional
- Control de expiración

### `004_create_activity_logs_table.sql`
Logs de actividad:
- Registro de acciones del sistema
- Información de contexto
- Metadatos de requests
- Categorización por niveles

### `005_create_permissions_table.sql`
Sistema de permisos RBAC:
- `permissions`: Permisos granulares
- `roles`: Roles del sistema
- `role_permissions`: Permisos por rol
- `user_roles`: Roles asignados a usuarios
- `user_permissions`: Permisos directos (excepciones)

### `006_create_api_tokens_table.sql`
Sistema de API:
- `api_tokens`: Tokens de autenticación
- `api_requests`: Logs de requests de API
- Control de rate limiting
- Estadísticas de uso

## Archivos de Seeds

### `001_default_permissions.sql`
Permisos por defecto organizados en categorías:
- `users`: Gestión de usuarios
- `roles`: Gestión de roles
- `content`: Gestión de contenido
- `system`: Configuración del sistema
- `api`: Acceso a API
- `reports`: Reportes
- `database`: Administración de BD

### `002_default_roles.sql`
Roles predefinidos:
- **super_admin**: Acceso completo
- **admin**: Administrador estándar
- **moderator**: Moderador de contenido
- **user**: Usuario estándar (por defecto)
- **guest**: Invitado con permisos mínimos

### `003_default_users.sql`
Usuarios de testing:
- **superadmin** / password (Super Admin)
- **admin** / password (Admin)
- **moderator** / password (Moderador)
- **demo** / demo (Demo User)
- **testuser** / test123 (Test User)

## Características del Sistema

### Sistema de Permisos RBAC
- Permisos granulares por funcionalidad
- Roles con conjuntos de permisos
- Permisos directos a usuarios (excepciones)
- Expiración temporal de roles/permisos

### Seguridad
- Contraseñas hasheadas con bcrypt
- Autenticación de dos factores opcional
- Control de intentos de login
- Bloqueo temporal de cuentas
- Tokens de API con scopes

### Auditoría
- Logs completos de actividad
- Tracking de sesiones
- Logs de requests de API
- Información de geolocalización

### Escalabilidad
- Índices optimizados para consultas frecuentes
- Soft deletes para preservar datos
- Soporte para múltiples conexiones de BD
- Sistema de migraciones

## Variables de Entorno

El sistema utiliza las siguientes variables del archivo `.env`:

```env
# Base de datos principal
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=docker_php_api
DB_USERNAME=root
DB_PASSWORD=root_password

# Conexiones adicionales
DB_SECONDARY_HOST=db_secondary
DB_ANALYTICS_HOST=analytics_db
# ... más conexiones según sea necesario
```

## Conexiones Múltiples

El sistema está preparado para usar múltiples bases de datos:
- **mysql**: Conexión principal
- **secondary**: Base de datos secundaria
- **analytics**: Base de datos de analytics
- **sqlite**: Base de datos SQLite para testing
- **pgsql**: PostgreSQL opcional

## Comandos Útiles

### Verificar el estado de la BD
```bash
docker compose exec db mysql -u root -p -e "SELECT COUNT(*) as usuarios FROM docker_php_api.users;"
```

### Ver migraciones ejecutadas
```bash
docker compose exec db mysql -u root -p -e "SELECT * FROM docker_php_api.migrations ORDER BY executed_at;"
```

### Resetear la BD (⚠️ CUIDADO - Elimina todos los datos)
```bash
docker compose exec db mysql -u root -p -e "DROP DATABASE docker_php_api; CREATE DATABASE docker_php_api;"
docker compose exec db mysql -u root -p docker_php_api < database/setup.sql
```

## Notas de Seguridad

### Para Desarrollo
- Los usuarios por defecto tienen contraseñas simples
- Todos los usuarios están activados
- No hay restricciones de IP

### Para Producción
1. **Cambiar todas las contraseñas por defecto**
2. **Eliminar usuarios de testing innecesarios**
3. **Configurar restricciones de IP en tokens de API**
4. **Habilitar 2FA para administradores**
5. **Revisar y ajustar permisos según necesidades**
6. **Configurar backups automáticos**

## Mantenimiento

### Limpieza de sesiones expiradas
```sql
DELETE FROM user_sessions WHERE expires_at < NOW();
```

### Limpieza de logs antiguos (ejemplo: más de 6 meses)
```sql
DELETE FROM activity_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 6 MONTH);
DELETE FROM api_requests WHERE created_at < DATE_SUB(NOW(), INTERVAL 6 MONTH);
```

### Usuarios inactivos (ejemplo: más de 1 año sin login)
```sql
UPDATE users SET status = 'inactive'
WHERE last_login_at < DATE_SUB(NOW(), INTERVAL 1 YEAR)
AND status = 'active';
```

## Soporte

Para preguntas sobre la estructura de la base de datos o migraciones, consulte:
- Documentación del proyecto
- Comentarios en los archivos SQL
- Logs de la aplicación