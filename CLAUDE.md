# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Architecture

This is a dockerized PHP application using FrankenPHP and MySQL 8:

- **docker/php/**: FrankenPHP container (PHP 8.2 with built-in web server)
- **src/**: PHP application source code (currently contains a simple index.php)

The stack includes:
- FrankenPHP as the web server (no need for separate Nginx)
- PHP 8.2 with MySQL PDO extensions and Composer
- MySQL 8.0 database container
- Xdebug configured for development

## Development Commands

### Starting the Environment
```bash
docker-compose up -d
```

### Stopping the Environment
```bash
docker-compose down
```

### Viewing Logs
```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f php
docker-compose logs -f db
```

### Rebuilding Containers
```bash
docker-compose build --no-cache
docker-compose up -d
```

### Accessing Containers
```bash
# PHP container
docker-compose exec php sh

# Database container
docker-compose exec db mysql -u root -p
```

### Using Composer
```bash
# Install dependencies
docker-compose exec php composer install

# Add new packages
docker-compose exec php composer require package/name

# Update dependencies
docker-compose exec php composer update
```

## Application Access

- **Web application**: http://localhost:${APP_PORT} (FrankenPHP)
- **Database**: localhost:${DB_PORT} (MySQL 8.0)

## Container Configuration

- **FrankenPHP**: Built-in web server with PHP 8.2, includes Composer
- **PHP**: Custom php.ini with 256M memory limit, 100M upload size, 600s execution time
- **Xdebug**: Enabled in development mode with client_host=host.docker.internal
- **Database**: MySQL 8.0 with environment variables for credentials

The application source code is mounted at `/var/www/html` in the php container for live development.