# 🗄️ Setup de Base de Datos - Docker PHP API

Este documento describe cómo configurar la base de datos usando los scripts automatizados.

## Scripts Disponibles

### 📜 `setup-db.sh` - Script Principal
Script completo para gestión de base de datos con múltiples opciones.

### 🛠️ `dev.sh` - Script de Desarrollo
Incluye comandos `db-setup` y `db-reset` que utilizan `setup-db.sh` internamente.

## Uso Rápido

### Opción 1: Script Dedicado (Recomendado)
```bash
# Setup completo - crea todo de una vez
./setup-db.sh

# Con contraseña predefinida
DB_PASSWORD=tu_password ./setup-db.sh
```

### Opción 2: Script de Desarrollo
```bash
# Integrado con el flujo de desarrollo
./dev.sh db-setup
```

## Opciones Disponibles

### `./setup-db.sh --full` (por defecto)
- ✅ Crea la base de datos si no existe
- ✅ Crea todas las tablas (12 tablas)
- ✅ Inserta permisos y roles del sistema
- ✅ Crea usuarios de prueba
- ✅ Configura relaciones y índices
- ✅ Muestra credenciales de acceso

### `./setup-db.sh --tables`
- ✅ Solo crea las estructuras de tablas
- ❌ No inserta datos
- Útil para entornos donde quieres datos limpios

### `./setup-db.sh --seeds`
- ❌ No crea tablas (deben existir)
- ✅ Solo inserta datos de ejemplo
- Útil para repoblar datos sin recrear estructura

### `./setup-db.sh --reset`
- ⚠️ **ELIMINA TODOS LOS DATOS EXISTENTES**
- ✅ Recrea la base de datos desde cero
- ✅ Ejecuta setup completo
- Requiere confirmación manual

### `./setup-db.sh --stats`
- 📊 Muestra estadísticas de la base de datos
- 📋 Lista tablas creadas
- 📈 Cuenta registros insertados

## Variables de Entorno

### `DB_PASSWORD`
```bash
# Configurar contraseña para evitar prompts
export DB_PASSWORD="tu_password"
./setup-db.sh

# O en una sola línea
DB_PASSWORD="tu_password" ./setup-db.sh
```

### Otras variables (automáticas)
- `DB_NAME`: docker_php_api
- `DB_USER`: root
- `DB_CONTAINER`: db

## Flujo de Trabajo Recomendado

### Primera Configuración
```bash
# 1. Iniciar contenedores
docker compose up -d

# 2. Configurar BD completa
./setup-db.sh

# 3. Verificar en la aplicación
# http://localhost:8080
# Login: demo/demo
```

### Durante Desarrollo
```bash
# Agregar datos de prueba adicionales
./setup-db.sh --seeds

# Ver estadísticas
./setup-db.sh --stats

# Resetear para pruebas limpias
./setup-db.sh --reset
```

### Para Testing
```bash
# Setup solo estructura (para tests automatizados)
./setup-db.sh --tables
```

## Usuarios Creados

Después del setup completo, estos usuarios están disponibles:

| Usuario      | Contraseña | Rol                    | Descripción |
|--------------|------------|------------------------|-------------|
| superadmin   | password   | Super Administrador    | Acceso completo |
| admin        | password   | Administrador          | Gestión general |
| moderator    | password   | Moderador              | Gestión contenido |
| demo         | demo       | Usuario Demo           | Testing básico |
| testuser     | test123    | Usuario de Prueba      | Development |

## Estructura Creada

### 👥 Sistema de Usuarios
- `users` - Usuarios principales
- `user_profiles` - Perfiles extendidos
- `user_sessions` - Sesiones activas

### 🔐 Sistema RBAC
- `roles` - Roles del sistema
- `permissions` - Permisos granulares
- `role_permissions` - Permisos por rol
- `user_roles` - Roles asignados
- `user_permissions` - Permisos directos

### 📊 Sistema de Auditoría
- `activity_logs` - Logs de actividad
- `api_requests` - Logs de API
- `api_tokens` - Tokens de autenticación

### 🔧 Sistema de Gestión
- `migrations` - Control de migraciones

## Troubleshooting

### Error: "Database not found"
```bash
# Verificar que el contenedor esté corriendo
docker compose ps

# Reiniciar contenedores
docker compose restart

# Verificar conexión
docker compose exec db mysql -u root -p -e "SHOW DATABASES;"
```

### Error: "Permission denied"
```bash
# Hacer script ejecutable
chmod +x setup-db.sh

# Verificar permisos
ls -la setup-db.sh
```

### Error: "Docker not running"
```bash
# Iniciar Docker
# En macOS: abrir Docker Desktop
# En Linux: sudo systemctl start docker

# Verificar estado
docker info
```

### Error: "Container not running"
```bash
# Iniciar contenedores
docker compose up -d

# Verificar estado
docker compose ps
```

## Scripts de Respaldo

### Crear Respaldo
```bash
# Backup completo
docker compose exec db mysqldump -u root -p docker_php_api > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Restaurar Respaldo
```bash
# Restaurar desde archivo
docker compose exec -T db mysql -u root -p docker_php_api < backup_file.sql
```

## Monitoreo

### Ver Logs del Setup
```bash
# Logs en tiempo real durante setup
docker compose logs -f db
```

### Verificar Tablas Creadas
```bash
./setup-db.sh --stats
```

### Conexión Manual
```bash
# Acceder a MySQL directamente
docker compose exec db mysql -u root -p docker_php_api

# Ver tablas
SHOW TABLES;

# Ver usuarios
SELECT username, email, role, status FROM users;
```

## Desarrollo de Scripts

### Estructura del Script
```bash
setup-db.sh
├── Validaciones de entorno
├── Funciones de configuración
├── Manejo de errores
├── Logging con colores
└── Confirmaciones de seguridad
```

### Extensión
Para agregar nuevas opciones, editar `setup-db.sh`:
1. Agregar opción en `show_help()`
2. Crear función específica
3. Agregar case en `main()`
4. Documentar en este archivo

---

**💡 Tip**: Usar `./setup-db.sh --help` para ver todas las opciones disponibles con ejemplos.