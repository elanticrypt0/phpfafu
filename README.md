# Docker PHP API

Una aplicación web PHP moderna construida con Flight Framework, Latte y HTMX. Incluye sistema de autenticación completo, validación de formularios avanzada y mensajes flash interactivos.

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
- **Respect\Validation** - Validación de formularios robusta
- **MySQL 8.0** - Base de datos
- **Docker** - Containerización completa

## Características Implementadas

### ✅ Backend
- **Estructura MVC** con controladores y middleware
- **Sistema de rutas** avanzado con Flight
- **Autenticación de usuarios** con sesiones PHP
- **Middleware de autenticación** reutilizable
- **APIs protegidas** con ejemplos funcionales
- **Validación de formularios** con Respect\Validation
- **Sistema de mensajes flash** con múltiples tipos
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

#### Autenticación
- `GET /` - Página principal
- `GET /login` - Formulario de login
- `POST /login` - Procesar autenticación
- `GET /dashboard` - Dashboard protegido
- `GET /logout` - Cerrar sesión
- `GET /api/protected-data` - API protegida
- `GET /api/user-profile` - Perfil de usuario

#### Ejemplos de Validación
- `GET /examples/registration` - Formulario de registro con validación
- `POST /examples/registration` - Procesar registro
- `GET /examples/contact` - Formulario de contacto
- `POST /examples/contact` - Procesar contacto
- `GET /examples/validation-demo` - Demo de mensajes flash
- `POST /examples/validation-demo` - Generar mensajes de prueba

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
│   │   ├── controllers/     # Auth, Home, Example Controllers
│   │   ├── middleware/      # AuthMiddleware
│   │   ├── helpers/         # FlashMessages, FormValidator
│   │   ├── views/           # Plantillas Latte (auth, examples)
│   │   └── config/          # Rutas y configuración
│   └── storage/cache/       # Cache de plantillas
├── docker/                  # Configuración Docker
├── dev.sh                   # Script de desarrollo
└── docker-compose.dev.yaml # Compose para desarrollo
```

## Ejemplos de Uso

### 📝 Validación de Formularios

#### Usar FormValidator Helper
```php
// En tu controlador
$validator = FormValidator::make($data)->validateMultiple([
    'email' => [
        'rules' => FormValidator::rules()->email(),
        'message' => 'Email debe ser válido'
    ],
    'password' => [
        'rules' => FormValidator::rules()->strongPassword(),
        'message' => 'Contraseña debe ser segura'
    ]
]);

if ($validator->fails()) {
    FlashMessages::error($validator->getFirstError());
    Flight::redirect('/formulario');
    return;
}

FlashMessages::success('¡Datos válidos!');
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

### 🔐 Crear un Controlador
```php
// src/app/controllers/MiController.php
class MiController extends BaseController {
    public function index(): void {
        FlashMessages::addToViews(); // Para mensajes flash

        Flight::view()->render('mi/vista.latte', [
            'title' => 'Mi Página',
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

Visita la aplicación para ver ejemplos funcionales de validación y mensajes flash:

### 📝 Formulario de Registro (`/examples/registration`)
- **Validaciones complejas:** Usuario alfanumérico (3-20 chars), email válido, contraseñas seguras
- **Confirmación de datos:** Verificación de contraseñas coincidentes
- **Validaciones opcionales:** URL de sitio web, edad numérica
- **Casos de prueba incluidos:** Ejemplos específicos para probar cada validación

### 📧 Formulario de Contacto (`/examples/contact`)
- **Validaciones básicas:** Nombre (solo letras), email requerido
- **Longitudes controladas:** Asunto (5-100 chars), mensaje (10-1000 chars)
- **Manejo de errores:** Mensajes específicos por campo

### 🎨 Demo de Mensajes Flash (`/examples/validation-demo`)
- **4 tipos de mensajes:** Success, Error, Warning, Info
- **Auto-ocultado:** Mensajes desaparecen automáticamente después de 5 segundos
- **Múltiples mensajes:** Prueba de varios mensajes simultáneos
- **Interactividad:** Botón de cerrar manual

### 🏠 Página Principal
- **Links mejorados:** Tarjetas informativas con descripción de cada ejemplo
- **Grid responsivo:** Layout que se adapta a móvil y desktop
- **Acceso rápido:** Botones directos a todos los ejemplos

## Licencia

MIT