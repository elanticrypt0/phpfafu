# Usar la imagen base de Nginx con Alpine Linux por su tamaño reducido
FROM nginx:alpine

# Instalar herramientas útiles para debugging y mantenimiento
RUN apk add --no-cache \
    curl \
    vim \
    bash \
    net-tools \
    iputils

# Eliminar la configuración predeterminada de Nginx
RUN rm /etc/nginx/conf.d/default.conf

# Crear directorio para los logs si no existe
RUN mkdir -p /var/log/nginx

# Configurar permisos para los logs
RUN chown -R nginx:nginx /var/log/nginx \
    && chmod -R 755 /var/log/nginx

# Copiar la configuración personalizada de Nginx
COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Exponer el puerto 80
EXPOSE 80

# Comando para iniciar Nginx en primer plano
CMD ["nginx", "-g", "daemon off;"]