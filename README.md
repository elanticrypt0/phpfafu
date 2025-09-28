# Docker PHP API

Una aplicación web PHP moderna construida con el framework Flight, plantillas Latte y HTMX con sistema de autenticación completo.

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
- **Latte** - Motor de plantillas de Nette
- **HTMX** - Interactividad HTML moderna sin JavaScript
- **MySQL 8.0** - Base de datos
- **Docker** - Containerización completa

## Características Implementadas

### ✅ Backend
- **Estructura MVC** con controladores y middleware
- **Sistema de rutas** avanzado con Flight
- **Autenticación de usuarios** con sesiones PHP
- **Middleware de autenticación** reutilizable
- **APIs protegidas** con ejemplos funcionales
- **Hot reload** para desarrollo

### ✅ Frontend
- **Plantillas Latte** con herencia de layouts
- **Integración HTMX** completa con estados de carga
- **CSS minimalista** y responsive con variables
- **Navegación dinámica** según estado de autenticación
- **Formularios interactivos** con validación

### ✅ DevOps
- **Configuración Docker** para desarrollo y producción
- **Cache de plantillas** automático
- **Logs estructurados** para debugging
- **Script de desarrollo** con comandos útiles

## Sistema de Autenticación

### Credenciales Demo
- **Usuario:** `demo`
- **Contraseña:** `demo`

### Funcionalidades
- ✅ Login/Logout con HTMX
- ✅ Rutas protegidas con middleware
- ✅ Dashboard con datos protegidos
- ✅ APIs autenticadas de ejemplo
- ✅ Redirecciones automáticas
- ✅ Manejo de sesiones seguro

### Rutas Disponibles
- `GET /` - Página principal
- `GET /login` - Formulario de login
- `POST /login` - Procesar autenticación
- `GET /dashboard` - Dashboard protegido
- `POST /logout` - Cerrar sesión
- `GET /api/protected-data` - API protegida
- `GET /api/user-profile` - Perfil de usuario

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
│   │   ├── controllers/     # AuthController, HomeController
│   │   ├── middleware/      # AuthMiddleware
│   │   ├── views/           # Plantillas Latte
│   │   └── config/          # Rutas y configuración
│   └── storage/cache/       # Cache de plantillas
├── docker/                  # Configuración Docker
├── dev.sh                   # Script de desarrollo
└── docker-compose.dev.yaml # Compose para desarrollo
```

## Ejemplos de Uso

### Crear un Controlador
```php
// src/app/controllers/MiController.php
class MiController extends BaseController {
    public function index(): void {
        Flight::view()->render('mi/vista.latte', [
            'title' => 'Mi Página',
            'currentUser' => Flight::get('currentUser'),
            'isAuthenticated' => Flight::get('isAuthenticated')
        ]);
    }
}
```

### Ruta Protegida
```php
// src/app/config/routes.php
Flight::route('GET /admin', function () {
    AuthMiddleware::authenticate(); // Requiere login
    $controller = Flight::miController();
    $controller->admin();
});
```

### Vista con HTMX
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

## Licencia

MIT