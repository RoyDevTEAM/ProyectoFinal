# Imagen base con PHP 8.2 y Apache
FROM php:8.2-apache

# -------------------------------
# Instalar dependencias del sistema
# -------------------------------
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    zlib1g-dev \
    libonig-dev \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql mbstring bcmath zip intl opcache xml \
    && rm -rf /var/lib/apt/lists/*

# -------------------------------
# Instalar Composer globalmente
# -------------------------------
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# -------------------------------
# Habilitar módulos de Apache
# -------------------------------
RUN a2enmod rewrite headers

# -------------------------------
# Establecer directorio de trabajo
# -------------------------------
WORKDIR /var/www/html

# -------------------------------
# Copiar archivos del proyecto
# -------------------------------
COPY . .

# -------------------------------
# Instalar dependencias de Laravel
# -------------------------------
RUN composer install --no-dev --optimize-autoloader

# -------------------------------
# Dar permisos correctos
# -------------------------------
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# -------------------------------
# Optimizar Laravel
# -------------------------------
RUN php artisan config:clear \
    && php artisan route:clear \
    && php artisan view:clear \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# -------------------------------
# Configurar Apache para Laravel
# -------------------------------
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|/var/www/|/var/www/html/public|g' /etc/apache2/apache2.conf

# -------------------------------
# Exponer puerto 80
# -------------------------------
EXPOSE 80

# -------------------------------
# Iniciar Apache
# -------------------------------
CMD ["apache2-foreground"]
