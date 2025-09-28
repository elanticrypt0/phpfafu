# Configuración de Desarrollo

## Inicio Rápido

```bash
# Clonar e iniciar
git clone <repository-url>
cd docker-php-api
docker compose up -d

# Acceder
http://localhost:8080
```

## Stack Tecnológico

- **[PHP Flight Framework](https://flightphp.com/)**
  Micro framework web para enrutamiento y desarrollo de APIs

- **[Latte Template Engine](https://latte.nette.org/en/)**
  Motor de plantillas rápido y seguro de Nette

- **[HTMX](https://htmx.org/)**
  Interacciones HTML modernas sin frameworks de JavaScript

## Comandos de Desarrollo

```bash
# Iniciar servicios
docker compose up -d

# Ver logs
docker compose logs -f php

# Acceder al contenedor PHP
docker compose exec php sh

# Instalar paquetes PHP
docker compose exec php composer require nombre/paquete

# Detener servicios
docker compose down

# Reconstruir contenedores
docker compose build --no-cache
```

## Estructura del Proyecto

```
src/
├── public/              # Directorio web público
│   ├── index.php        # Punto de entrada
│   └── assets/          # Archivos estáticos
│       ├── css/         # Hojas de estilo
│       └── js/          # JavaScript (HTMX)
├── app/
│   ├── config/          # Configuración del framework
│   │   ├── routes.php   # Definición de rutas
│   │   └── classes.php  # Registro de clases/controladores
│   ├── controllers/     # Controladores MVC
│   │   ├── BaseController.php
│   │   └── HomeController.php
│   └── views/           # Plantillas Latte
│       ├── layouts/     # Layouts base
│       └── home/        # Vistas específicas
├── storage/
│   └── cache/           # Cache de plantillas Latte
└── vendor/              # Dependencias de Composer
```

## Arquitectura

### Controladores
Los controladores se registran en `app/config/classes.php` y se extienden de `BaseController`:

```php
// Registro en classes.php
Flight::register('homeController', HomeController::class);

// Uso en rutas
$homeController = Flight::homeController();
$homeController->saludar($nombre);
```

### Rutas
Definidas en `app/config/routes.php` con soporte para métodos HTTP específicos:

```php
Flight::route('/', function () {
    Flight::view()->render('home/home.latte', ['title' => 'Mi App']);
});

Flight::route('POST /saludar', function () {
    // Lógica del endpoint
});
```

### Plantillas
Sistema Latte con layouts base y herencia de plantillas:

```latte
{* Extiende el layout base *}
{extends '../layouts/Base.latte'}

{block content}
    <!-- Contenido específico -->
{/block}
```

## Configuración

- **Servidor Web**: FrankenPHP (integrado)
- **PHP**: 8.2 con extensiones PDO MySQL y Composer
- **Base de Datos**: MySQL 8.0
- **Debug**: Xdebug habilitado
- **Cache**: Sistema de cache automático para plantillas Latte
- **Estilos**: CSS global minimalista con variables CSS