#!/bin/bash

# =============================================================================
# Script de Setup de Base de Datos - Docker PHP API
# =============================================================================
#
# Este script automatiza la configuración de la base de datos para el proyecto
# Docker PHP API con Eloquent ORM y Laravel Validator.
#
# Uso:
#   ./setup-db.sh [opciones]
#
# Opciones:
#   --full      Setup completo (tablas + datos)
#   --tables    Solo crear tablas
#   --seeds     Solo insertar datos
#   --reset     Resetear BD completamente (⚠️  ELIMINA TODOS LOS DATOS)
#   --help      Mostrar ayuda
#
# =============================================================================

set -e  # Salir si hay errores

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Configuración
DB_NAME="docker_php_api"
DB_USER="root"
DB_CONTAINER="db"

# Función para mostrar mensajes con colores
log_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

log_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

log_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

log_error() {
    echo -e "${RED}❌ $1${NC}"
}

log_title() {
    echo -e "${PURPLE}🚀 $1${NC}"
}

# Función para verificar si Docker está corriendo
check_docker() {
    if ! docker info >/dev/null 2>&1; then
        log_error "Docker no está corriendo. Inicia Docker primero."
        exit 1
    fi
}

# Función para verificar si el contenedor de BD está corriendo
check_db_container() {
    if ! docker compose ps | grep -q "$DB_CONTAINER.*running"; then
        log_error "El contenedor de base de datos no está corriendo."
        log_info "Ejecuta: docker compose up -d"
        exit 1
    fi
}

# Función para verificar si la BD existe
check_database() {
    log_info "Verificando si la base de datos '$DB_NAME' existe..."

    if docker compose exec -T $DB_CONTAINER mysql -u $DB_USER -p$DB_PASSWORD -e "USE $DB_NAME;" 2>/dev/null; then
        log_success "Base de datos '$DB_NAME' encontrada."
        return 0
    else
        log_warning "Base de datos '$DB_NAME' no encontrada."
        return 1
    fi
}

# Función para crear la base de datos
create_database() {
    log_info "Creando base de datos '$DB_NAME'..."

    docker compose exec -T $DB_CONTAINER mysql -u $DB_USER -p$DB_PASSWORD -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

    if [ $? -eq 0 ]; then
        log_success "Base de datos '$DB_NAME' creada exitosamente."
    else
        log_error "Error al crear la base de datos."
        exit 1
    fi
}

# Función para ejecutar archivo SQL
execute_sql_file() {
    local file=$1
    local description=$2

    if [ ! -f "$file" ]; then
        log_error "Archivo no encontrado: $file"
        return 1
    fi

    log_info "$description..."

    if docker compose exec -T $DB_CONTAINER mysql -u $DB_USER -p$DB_PASSWORD $DB_NAME < "$file"; then
        log_success "$description completado."
        return 0
    else
        log_error "Error ejecutando: $file"
        return 1
    fi
}

# Función para setup completo
setup_full() {
    log_title "Setup Completo de Base de Datos"

    check_docker
    check_db_container

    # Leer contraseña si no está configurada
    if [ -z "$DB_PASSWORD" ]; then
        echo -n "Ingresa la contraseña de MySQL root: "
        read -s DB_PASSWORD
        echo
        export DB_PASSWORD
    fi

    # Crear BD si no existe
    if ! check_database; then
        create_database
    fi

    # Ejecutar setup completo
    execute_sql_file "database/setup.sql" "Ejecutando setup completo"

    if [ $? -eq 0 ]; then
        log_success "🎉 Setup completo terminado exitosamente!"
        show_credentials
    else
        log_error "Falló el setup completo."
        exit 1
    fi
}

# Función para crear solo tablas
setup_tables() {
    log_title "Creando Solo Tablas"

    check_docker
    check_db_container

    if [ -z "$DB_PASSWORD" ]; then
        echo -n "Ingresa la contraseña de MySQL root: "
        read -s DB_PASSWORD
        echo
        export DB_PASSWORD
    fi

    if ! check_database; then
        create_database
    fi

    execute_sql_file "database/migrations/001_initial_migration.sql" "Creando tablas"

    if [ $? -eq 0 ]; then
        log_success "✅ Tablas creadas exitosamente!"
    else
        log_error "Error creando tablas."
        exit 1
    fi
}

# Función para insertar solo datos
setup_seeds() {
    log_title "Insertando Datos de Ejemplo"

    check_docker
    check_db_container

    if [ -z "$DB_PASSWORD" ]; then
        echo -n "Ingresa la contraseña de MySQL root: "
        read -s DB_PASSWORD
        echo
        export DB_PASSWORD
    fi

    if ! check_database; then
        log_error "La base de datos no existe. Ejecuta primero --tables o --full"
        exit 1
    fi

    execute_sql_file "database/migrations/002_seed_initial_data.sql" "Insertando datos de ejemplo"

    if [ $? -eq 0 ]; then
        log_success "✅ Datos insertados exitosamente!"
        show_credentials
    else
        log_error "Error insertando datos."
        exit 1
    fi
}

# Función para resetear BD
reset_database() {
    log_title "Reseteando Base de Datos"
    log_warning "⚠️  ESTO ELIMINARÁ TODOS LOS DATOS DE LA BASE DE DATOS ⚠️"
    echo
    echo -n "¿Estás seguro? Escribe 'RESETEAR' para confirmar: "
    read confirmation

    if [ "$confirmation" != "RESETEAR" ]; then
        log_info "Operación cancelada."
        exit 0
    fi

    check_docker
    check_db_container

    if [ -z "$DB_PASSWORD" ]; then
        echo -n "Ingresa la contraseña de MySQL root: "
        read -s DB_PASSWORD
        echo
        export DB_PASSWORD
    fi

    log_info "Eliminando base de datos '$DB_NAME'..."
    docker compose exec -T $DB_CONTAINER mysql -u $DB_USER -p$DB_PASSWORD -e "DROP DATABASE IF EXISTS $DB_NAME;"

    log_info "Recreando base de datos..."
    create_database

    log_info "Ejecutando setup completo..."
    execute_sql_file "database/setup.sql" "Setup después del reset"

    if [ $? -eq 0 ]; then
        log_success "🎉 Base de datos reseteada y configurada exitosamente!"
        show_credentials
    else
        log_error "Error en el reset."
        exit 1
    fi
}

# Función para mostrar credenciales
show_credentials() {
    echo
    log_title "👥 Usuarios Disponibles para Testing"
    echo -e "${CYAN}┌─────────────────────────────────────────────────────────┐${NC}"
    echo -e "${CYAN}│ Usuario      │ Contraseña │ Rol                        │${NC}"
    echo -e "${CYAN}├─────────────────────────────────────────────────────────┤${NC}"
    echo -e "${CYAN}│ superadmin   │ password   │ Super Administrador        │${NC}"
    echo -e "${CYAN}│ admin        │ password   │ Administrador              │${NC}"
    echo -e "${CYAN}│ moderator    │ password   │ Moderador                  │${NC}"
    echo -e "${CYAN}│ demo         │ demo       │ Usuario Demo               │${NC}"
    echo -e "${CYAN}│ testuser     │ test123    │ Usuario de Prueba          │${NC}"
    echo -e "${CYAN}└─────────────────────────────────────────────────────────┘${NC}"
    echo
    log_info "Accede a la aplicación en: ${CYAN}http://localhost:8080${NC}"
    log_info "Ver ejemplos en: ${CYAN}http://localhost:8080/examples${NC}"
    log_info "Gestión de BD en: ${CYAN}http://localhost:8080/database/connections${NC}"
    echo
}

# Función para mostrar estadísticas
show_stats() {
    if [ -z "$DB_PASSWORD" ]; then
        echo -n "Ingresa la contraseña de MySQL root: "
        read -s DB_PASSWORD
        echo
        export DB_PASSWORD
    fi

    log_title "📊 Estadísticas de la Base de Datos"

    echo -e "${CYAN}Tablas creadas:${NC}"
    docker compose exec -T $DB_CONTAINER mysql -u $DB_USER -p$DB_PASSWORD $DB_NAME -e "SHOW TABLES;" 2>/dev/null | grep -v "Tables_in" | while read table; do
        echo "  • $table"
    done

    echo
    echo -e "${CYAN}Datos insertados:${NC}"
    docker compose exec -T $DB_CONTAINER mysql -u $DB_USER -p$DB_PASSWORD $DB_NAME -e "
        SELECT 'Usuarios' as Tabla, COUNT(*) as Registros FROM users
        UNION SELECT 'Roles', COUNT(*) FROM roles
        UNION SELECT 'Permisos', COUNT(*) FROM permissions
        UNION SELECT 'Migraciones', COUNT(*) FROM migrations;
    " 2>/dev/null | column -t
}

# Función para mostrar ayuda
show_help() {
    echo -e "${BLUE}🐳 Setup de Base de Datos - Docker PHP API${NC}"
    echo
    echo -e "${YELLOW}Uso:${NC}"
    echo "  ./setup-db.sh [opción]"
    echo
    echo -e "${YELLOW}Opciones:${NC}"
    echo "  --full      Setup completo (tablas + datos) [POR DEFECTO]"
    echo "  --tables    Solo crear tablas"
    echo "  --seeds     Solo insertar datos"
    echo "  --reset     Resetear BD completamente (⚠️  ELIMINA TODOS LOS DATOS)"
    echo "  --stats     Mostrar estadísticas de la BD"
    echo "  --help      Mostrar esta ayuda"
    echo
    echo -e "${YELLOW}Variables de entorno:${NC}"
    echo "  DB_PASSWORD  Contraseña de MySQL (opcional, se pedirá si no está configurada)"
    echo
    echo -e "${YELLOW}Ejemplos:${NC}"
    echo "  ./setup-db.sh                    # Setup completo"
    echo "  ./setup-db.sh --tables           # Solo crear tablas"
    echo "  DB_PASSWORD=secret ./setup-db.sh # Con contraseña"
    echo
    echo -e "${YELLOW}Requisitos:${NC}"
    echo "  • Docker y Docker Compose corriendo"
    echo "  • Contenedor 'db' activo (docker compose up -d)"
    echo
}

# Función principal
main() {
    case "${1:-}" in
        --full|"")
            setup_full
            ;;
        --tables)
            setup_tables
            ;;
        --seeds)
            setup_seeds
            ;;
        --reset)
            reset_database
            ;;
        --stats)
            show_stats
            ;;
        --help|-h)
            show_help
            ;;
        *)
            log_error "Opción desconocida: $1"
            echo
            show_help
            exit 1
            ;;
    esac
}

# Verificar que estamos en el directorio correcto
if [ ! -f "database/setup.sql" ]; then
    log_error "No se encontró database/setup.sql"
    log_info "Asegúrate de ejecutar este script desde el directorio raíz del proyecto."
    exit 1
fi

# Ejecutar función principal
main "$@"