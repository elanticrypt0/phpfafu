# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Architecture

This is a dockerized PHP application using FrankenPHP, MySQL 8, Eloquent ORM, and Laravel Validator:

- **docker/php/**: FrankenPHP container (PHP 8.2 with built-in web server)
- **src/**: PHP application source code with modern MVC structure
- **database/**: SQL schemas, seeds, and migrations

The stack includes:
- **FrankenPHP** as the web server (no need for separate Nginx)
- **PHP 8.2** with MySQL PDO extensions and Composer
- **MySQL 8.0** database container with multiple connection support
- **Eloquent ORM** for database operations and relationships
- **Laravel Validator** for form validation and data integrity
- **FlightPHP** framework for routing and MVC structure
- **Latte** templating engine for views
- **Xdebug** configured for development

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

## Database Setup

### Quick Setup
```bash
# Setup complete database with all tables and sample data
docker compose exec db mysql -u root -p docker_php_api < database/setup.sql
```

### Manual Setup
```bash
# Create tables only
docker compose exec db mysql -u root -p docker_php_api < database/migrations/001_initial_migration.sql

# Add sample data
docker compose exec db mysql -u root -p docker_php_api < database/migrations/002_seed_initial_data.sql
```

## Application Features

### Authentication System
- User registration and login
- Role-based access control (RBAC)
- Session management
- Two-factor authentication support

### Database Management
- Multiple database connections
- Eloquent ORM integration
- Laravel Validator for data validation
- Database query runner and connection testing
- Activity logging and audit trails

### API System
- RESTful API endpoints
- Token-based authentication
- Rate limiting and request logging
- API documentation and examples

### User Management
- User profiles and settings
- Permission system with granular control
- User activity tracking
- Soft deletes and data preservation

## Default Users

After running the database setup, these users are available for testing:

- **superadmin** / password (Super Administrator)
- **admin** / password (Administrator)
- **moderator** / password (Moderator)
- **demo** / demo (Demo User)
- **testuser** / test123 (Test User)

## Available Routes

### Web Interface
- `/` - Home page
- `/login` - User authentication
- `/dashboard` - User dashboard (requires auth)
- `/database/connections` - Database management (requires auth)
- `/examples` - Eloquent & Laravel Validator examples (requires auth)

### API Endpoints
- `/api/examples/?example=<type>` - Run specific example
- `/api/examples/all` - Run all examples

## Environment Configuration

Copy `.env.example` to `.env` and configure:

```env
# Database settings
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=docker_php_api
DB_USERNAME=root
DB_PASSWORD=root_password

# Application settings
APP_NAME="Docker PHP API"
APP_ENV=local
APP_DEBUG=true
```

## Code Structure

```
src/
├── app/
│   ├── config/          # Configuration files
│   │   ├── eloquent.php # Eloquent ORM setup
│   │   ├── database.php # Database connections
│   │   └── routes/      # Route definitions
│   ├── controllers/     # MVC controllers
│   ├── models/          # Eloquent models
│   ├── helpers/         # Helper classes
│   ├── examples/        # Usage examples
│   └── views/           # Latte templates
├── public/              # Web root
│   └── index.php        # Application entry point
├── vendor/              # Composer dependencies
├── .env                 # Environment configuration
└── .env.example         # Environment template

database/
├── schema/              # Table definitions
├── seeds/               # Initial data
├── migrations/          # Migration scripts
├── setup.sql            # Complete setup script
└── README.md            # Database documentation
```

## Key Classes

- **EloquentManager**: Manages multiple database connections
- **FormValidator**: Laravel Validator wrapper with Spanish translations
- **User**: Eloquent model with authentication and RBAC
- **BaseModel**: Base class for models with connection switching
- **AuthController**: Handles authentication and authorization
- **DatabaseController**: Database management interface
- **ExamplesController**: Demonstrates new implementations

## Development Workflow

1. **Database Setup**: Run `database/setup.sql` for complete setup
2. **Authentication**: Use demo/demo for quick testing
3. **Examples**: Visit `/examples` to see Eloquent and Laravel Validator in action
4. **Database Management**: Use `/database/connections` for connection testing
5. **API Testing**: Use `/api/examples/` endpoints for programmatic access