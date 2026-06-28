# Usar PHP con Apache
FROM php:8.1-apache
# Instala extensiones necesarias
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install zip

RUN apt-get update && \
    apt-get install -y libreoffice && \
    apt-get clean

# Instalar extensiones necesarias (ejemplo: mysqli)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copiar archivos del proyecto al contenedor
COPY . /var/www/html/

# Dar permisos adecuados
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Exponer el puerto 80 para el servidor Apache
EXPOSE 80 
EXPOSE 443

RUN a2enmod ssl
RUN a2ensite default-ssl

# Copiar configuración SSL personalizada
COPY apache/default-ssl.conf /etc/apache2/sites-available/default-ssl.conf

# Crear carpeta para certificados
RUN mkdir -p /etc/apache2/ssl