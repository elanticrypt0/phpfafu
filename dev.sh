#!/bin/bash

# Script para desarrollo con hot reload
# Uso: ./dev.sh [comando]

set -e

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Función para mostrar ayuda
show_help() {
    echo -e "${BLUE}🚀 Script de Desarrollo Docker PHP API${NC}"
    echo ""
    echo "Comandos disponibles:"
    echo -e "  ${GREEN}start${NC}     - Iniciar en modo desarrollo con hot reload"
    echo -e "  ${GREEN}stop${NC}      - Detener servicios de desarrollo"
    echo -e "  ${GREEN}restart${NC}   - Reiniciar servicios de desarrollo"
    echo -e "  ${GREEN}logs${NC}      - Ver logs en tiempo real"
    echo -e "  ${GREEN}shell${NC}     - Acceder al contenedor PHP"
    echo -e "  ${GREEN}composer${NC}  - Ejecutar comandos de Composer"
    echo -e "  ${GREEN}clean${NC}     - Limpiar contenedores y volúmenes"
    echo -e "  ${GREEN}build${NC}     - Reconstruir contenedores"
    echo -e "  ${GREEN}status${NC}    - Ver estado de los servicios"
    echo ""
    echo "Ejemplos:"
    echo -e "  ${YELLOW}./dev.sh start${NC}                    # Iniciar desarrollo"
    echo -e "  ${YELLOW}./dev.sh composer install${NC}        # Instalar dependencias"
    echo -e "  ${YELLOW}./dev.sh composer require vendor/pkg${NC} # Agregar paquete"
}

# Crear directorio de logs si no existe
mkdir -p logs

case "${1:-help}" in
    start)
        echo -e "${GREEN}🚀 Iniciando entorno de desarrollo...${NC}"
        docker compose -f docker-compose.dev.yaml up -d
        echo -e "${GREEN}✅ Servicios iniciados en modo desarrollo${NC}"
        echo -e "${BLUE}📡 Aplicación disponible en: http://localhost:8080${NC}"
        echo -e "${YELLOW}💡 Los cambios se reflejan automáticamente (hot reload)${NC}"
        ;;

    stop)
        echo -e "${YELLOW}🛑 Deteniendo servicios...${NC}"
        docker compose -f docker-compose.dev.yaml down
        echo -e "${GREEN}✅ Servicios detenidos${NC}"
        ;;

    restart)
        echo -e "${YELLOW}🔄 Reiniciando servicios...${NC}"
        docker compose -f docker-compose.dev.yaml restart
        echo -e "${GREEN}✅ Servicios reiniciados${NC}"
        ;;

    logs)
        echo -e "${BLUE}📋 Mostrando logs (Ctrl+C para salir)...${NC}"
        docker compose -f docker-compose.dev.yaml logs -f
        ;;

    shell)
        echo -e "${BLUE}🐚 Accediendo al contenedor PHP...${NC}"
        docker compose -f docker-compose.dev.yaml exec php sh
        ;;

    composer)
        shift
        if [ $# -eq 0 ]; then
            echo -e "${RED}❌ Especifica un comando de Composer${NC}"
            echo -e "${YELLOW}Ejemplo: ./dev.sh composer install${NC}"
            exit 1
        fi
        echo -e "${BLUE}📦 Ejecutando: composer $*${NC}"
        docker compose -f docker-compose.dev.yaml exec php composer "$@"
        ;;

    clean)
        echo -e "${YELLOW}🧹 Limpiando contenedores y volúmenes...${NC}"
        docker compose -f docker-compose.dev.yaml down -v
        docker system prune -f
        echo -e "${GREEN}✅ Limpieza completada${NC}"
        ;;

    build)
        echo -e "${YELLOW}🔨 Reconstruyendo contenedores...${NC}"
        docker compose -f docker-compose.dev.yaml build --no-cache
        echo -e "${GREEN}✅ Contenedores reconstruidos${NC}"
        ;;

    status)
        echo -e "${BLUE}📊 Estado de los servicios:${NC}"
        docker compose -f docker-compose.dev.yaml ps
        ;;

    help|--help|-h)
        show_help
        ;;

    *)
        echo -e "${RED}❌ Comando desconocido: $1${NC}"
        echo ""
        show_help
        exit 1
        ;;
esac