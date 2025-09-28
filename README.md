# Docker PHP API

Una aplicación web PHP moderna construida con Flight Framework, Eloquent ORM, Laravel Validator y Latte. Incluye sistema de autenticación completo, gestión de múltiples bases de datos y ejemplos prácticos de uso.

## Instalación Rápida

### Producción
```bash
docker compose up -d
```

### Desarrollo (con hot reload)
```bash
./dev.sh start
```

**Visitar:** http://localhost:8080

## Stack Tecnológico

- **PHP 8.2** con FrankenPHP
- **Flight Framework** - Micro framework para APIs y routing
- **Eloquent ORM** - ORM de Laravel para bases de datos
- **Laravel Validator** - Sistema de validación robusto con localización
- **Latte** - Motor de plantillas de Nette
- **HTMX** - Interactividad HTML moderna sin JavaScript
- **MySQL 8.0** - Base de datos con soporte para múltiples conexiones
- **Docker** - Containerización completa

## Características Implementadas

### ✅ Backend
- **Estructura MVC** con controladores y middleware
- **Eloquent ORM** integrado con múltiples conexiones de BD
- **Laravel Validator** con mensajes en español y validaciones avanzadas
- **Sistema RBAC** completo con roles y permisos granulares
- **Autenticación de usuarios** con sesiones PHP y tokens API
- **Middleware de autenticación** reutilizable
- **APIs protegidas** con documentación y ejemplos
- **Sistema de logging** y auditoría de actividades
- **Transacciones de base de datos** y operaciones atómicas
- **Hot reload** para desarrollo

### ✅ Frontend
- **Plantillas Latte** con herencia de layouts
- **Integración HTMX** completa con estados de carga
- **CSS minimalista** y responsive con variables
- **Navegación dinámica** según estado de autenticación
- **Formularios interactivos** con validación
- **Mensajes flash** con auto-ocultado y estilos

### ✅ DevOps
- **Configuración Docker** para desarrollo y producción
- **Cache de plantillas** automático
- **Logs estructurados** para debugging
- **Script de desarrollo** con comandos útiles

## Sistema de Autenticación

### Credenciales Demo
Después de ejecutar el setup de base de datos, usa estos usuarios para testing:

- **superadmin** / password (Super Administrador - acceso completo)
- **admin** / password (Administrador - gestión general)
- **moderator** / password (Moderador - gestión de contenido)
- **demo** / demo (Usuario Demo - acceso básico)
- **testuser** / test123 (Usuario de Prueba)

### Funcionalidades
- ✅ Sistema RBAC con roles y permisos granulares
- ✅ Login/Logout con validación Laravel Validator
- ✅ Rutas protegidas con middleware
- ✅ Dashboard con datos protegidos
- ✅ APIs autenticadas con tokens
- ✅ Gestión de sesiones activas
- ✅ Logs de actividad y auditoría
- ✅ Soporte para 2FA (preparado)

## Setup de Base de Datos

### Configuración Rápida
```bash
# 1. Configurar variables de entorno
cp src/.env.example src/.env

# 2. Configurar la base de datos completa
docker compose exec db mysql -u root -p docker_php_api < database/setup.sql
```

### Configuración Manual
```bash
# Crear solo las tablas
docker compose exec db mysql -u root -p docker_php_api < database/migrations/001_initial_migration.sql

# Agregar datos de ejemplo
docker compose exec db mysql -u root -p docker_php_api < database/migrations/002_seed_initial_data.sql
```

### Rutas Disponibles

#### Autenticación
- `GET /` - Página principal
- `GET /login` - Formulario de login
- `POST /login` - Procesar autenticación
- `GET /dashboard` - Dashboard protegido
- `GET /logout` - Cerrar sesión

#### Gestión de Base de Datos
- `GET /database/connections` - Ver conexiones de BD
- `POST /database/test-connection` - Probar conexión específica
- `GET /database/query-runner` - Ejecutor de queries
- `POST /database/execute-query` - Ejecutar query SQL
- `GET /database/examples` - Ejemplos de uso de BD

#### Ejemplos de Eloquent & Laravel Validator
- `GET /examples` - Índice de ejemplos
- `POST /examples/run` - Ejecutar ejemplo específico
- `GET /examples/docs` - Documentación de ejemplos

#### API Endpoints
- `GET /api/examples/?example=<tipo>` - Ejecutar ejemplo específico
- `GET /api/examples/all` - Ejecutar todos los ejemplos

## Desarrollo

### Comandos Rápidos
```bash
# Iniciar desarrollo con hot reload
./dev.sh start

# Ver logs en tiempo real
./dev.sh logs

# Acceder al contenedor PHP
./dev.sh shell

# Instalar dependencias
./dev.sh composer install

# Detener servicios
./dev.sh stop

# Ver ayuda completa
./dev.sh help
```

### Modo Desarrollo vs Producción
- **Desarrollo:** Hot reload, logs detallados, sin cache
- **Producción:** Optimizado, cache habilitado, logs mínimos

Ver [README_DEV.md](README_DEV.md) para documentación técnica detallada.

## Estructura del Proyecto

```
├── src/
│   ├── public/              # Directorio web público
│   │   ├── index.php        # Punto de entrada
│   │   └── assets/          # CSS, JS, imágenes
│   ├── app/
│   │   ├── controllers/     # Auth, Database, Examples Controllers
│   │   ├── models/          # Modelos Eloquent (User, etc.)
│   │   ├── middleware/      # AuthMiddleware
│   │   ├── helpers/         # FlashMessages, FormValidator (Laravel)
│   │   ├── examples/        # Ejemplos de uso de Eloquent y Laravel Validator
│   │   ├── views/           # Plantillas Latte (auth, database, examples)
│   │   └── config/          # Eloquent, database, rutas
│   ├── storage/cache/       # Cache de plantillas
│   ├── .env                 # Variables de entorno
│   └── .env.example         # Template de variables
├── database/                # Setup de base de datos
│   ├── schema/              # Definiciones de tablas
│   ├── seeds/               # Datos iniciales
│   ├── migrations/          # Scripts de migración
│   ├── setup.sql            # Setup completo
│   └── README.md            # Documentación de BD
├── docker/                  # Configuración Docker
├── dev.sh                   # Script de desarrollo
└── docker-compose.dev.yaml # Compose para desarrollo
```

## Ejemplos de Uso

### 📝 Validación con Laravel Validator

#### Validación Básica
```php
// Usando el FormValidator actualizado con Laravel Validator
$validator = FormValidator::make($data)->validate([
    'email' => 'required|email',
    'password' => 'required|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
    'age' => 'required|integer|between:18,100'
], [
    'email.email' => 'El email debe ser válido',
    'password.regex' => 'La contraseña debe contener mayúsculas, minúsculas y números',
    'age.between' => 'La edad debe estar entre 18 y 100 años'
]);

if ($validator->fails()) {
    FlashMessages::error($validator->getFirstError());
    Flight::redirect('/formulario');
    return;
}

FlashMessages::success('¡Datos válidos!');
```

#### Validaciones Específicas Predefinidas
```php
// Validación de login
$validator = FormValidator::validateLogin($data);

// Validación de registro
$validator = FormValidator::validateRegistration($data);

// Validación de contraseña fuerte
$validator = FormValidator::validateStrongPassword($data);
```

#### Mensajes Flash
```php
// Diferentes tipos de mensajes
FlashMessages::success('Operación exitosa');
FlashMessages::error('Error al procesar');
FlashMessages::warning('Revisa los datos');
FlashMessages::info('Información adicional');

// Los mensajes se muestran automáticamente en las vistas
// y se auto-ocultan después de 5 segundos
```

### 🗄️ Usar Eloquent ORM

#### Operaciones Básicas con el Modelo User
```php
// Crear usuario
$user = User::create([
    'username' => 'nuevo_usuario',
    'email' => 'usuario@example.com',
    'password' => 'password123', // Se hashea automáticamente
    'first_name' => 'Nombre',
    'last_name' => 'Apellido'
]);

// Buscar usuario
$user = User::findByEmail('usuario@example.com');
$user = User::findByUsername('nuevo_usuario');

// Autenticar usuario
$user = User::authenticate('usuario@example.com', 'password123');

// Búsqueda con filtros
$users = User::search('término')->get();
$users = User::active()->get();
```

#### Múltiples Conexiones de Base de Datos
```php
// Usar conexión principal (por defecto)
$users = User::all();

// Usar conexión específica
$users = User::on('secondary')->all();
$users = User::on('analytics')->all();

// Cambiar conexión dinámicamente
$user = new User();
$user->setConnection('analytics');
$analyticsUsers = $user->all();
```

#### Transacciones
```php
// Transacción simple
EloquentManager::transaction(function () {
    $user = User::create($userData);
    $profile = UserProfile::create(['user_id' => $user->id, ...]);
    return $user;
});

// Transacción en conexión específica
EloquentManager::transaction(function () {
    // Operaciones en transacción
}, 'secondary');
```

### 🔐 Crear un Controlador
```php
// src/app/controllers/MiController.php
require_once __DIR__."/../models/User.php";

class MiController extends BaseController {
    public function index(): void {
        FlashMessages::addToViews();

        // Usar Eloquent
        $users = User::active()->take(10)->get();

        Flight::view()->render('mi/vista.latte', [
            'title' => 'Mi Página',
            'users' => $users,
            'currentUser' => Flight::get('currentUser'),
            'isAuthenticated' => Flight::get('isAuthenticated')
        ]);
    }
}
```

### 🛡️ Ruta Protegida
```php
// src/app/config/routes.php
Flight::route('GET /admin', function () {
    AuthMiddleware::authenticate(); // Requiere login
    $controller = Flight::miController();
    $controller->admin();
});
```

### 🎨 Vista con HTMX
```latte
{* src/app/views/mi/vista.latte *}
{extends '../layouts/Base.latte'}

{block content}
<button hx-get="/api/datos" hx-target="#resultado">
    Cargar Datos
</button>
<div id="resultado"></div>
{/block}
```

### 📋 Formulario con Validación (sin HTMX)
```latte
<form method="POST" action="/mi-ruta">
    <input type="email" name="email" required>
    <input type="password" name="password" required>
    <button type="submit">Enviar</button>
</form>
```

## 🧪 Ejemplos Interactivos

Visita la aplicación para ver ejemplos funcionales de Eloquent ORM y Laravel Validator:

### 🚀 Ejemplos de Eloquent ORM (`/examples`)
- **Crear Usuario:** Validación con Laravel Validator + creación con Eloquent
- **Autenticación:** Sistema de login usando modelos Eloquent
- **Búsqueda de Usuarios:** Filtros y paginación con Eloquent
- **Múltiples Conexiones:** Demostración de conexiones múltiples de BD
- **Transacciones:** Operaciones atómicas con rollback automático
- **Validaciones Avanzadas:** Reglas complejas con mensajes personalizados
- **Estadísticas del Sistema:** Información en tiempo real de conexiones y usuarios

### 🗄️ Gestión de Base de Datos (`/database/connections`)
- **Conexiones Múltiples:** Visualización de todas las conexiones configuradas
- **Prueba de Conexiones:** Test de conectividad en tiempo real
- **Ejecutor de Queries:** Interface para ejecutar SQL directamente
- **Información del Servidor:** Versiones, estadísticas y metadatos

### 🔌 API Endpoints
- **API de Ejemplos:** Endpoints RESTful para todos los ejemplos
- **Respuestas JSON:** Datos estructurados para integración
- **Documentación Automática:** Ejemplos de uso y parámetros

#### Endpoints Disponibles:
```bash
# Ejecutar ejemplo específico
GET /api/examples/?example=create_user
GET /api/examples/?example=authenticate
GET /api/examples/?example=search_users
GET /api/examples/?example=multiple_connections
GET /api/examples/?example=transaction
GET /api/examples/?example=advanced_validation
GET /api/examples/?example=system_stats

# Ejecutar todos los ejemplos
GET /api/examples/all
```

## Licencia

MIT