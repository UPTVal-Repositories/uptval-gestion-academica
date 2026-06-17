FROM php:8.2-apache

# 1. Instalar dependencias del sistema operativo (unzip, zip, curl, git)
RUN apt-get update && apt-get install -y \
    unzip \
    zip \
    curl \
    git \
    && rm -rf /var/lib/apt/lists/*

# 2. Habilitar mod_rewrite para las URLs amigables
RUN a2enmod rewrite

# 3. Instalar extensiones de PHP (Base de datos)
RUN docker-php-ext-install pdo pdo_mysql

# 4. Instalar Composer copiando el binario desde la imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Configurar el DocumentRoot de Apache apuntando a la carpeta public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf