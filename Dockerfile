# -------------------------------
# Imagen base PHP 8.2
# -------------------------------
FROM php:8.2-cli

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
# Puerto asignado por Render
# -------------------------------
ENV PORT 10000
EXPOSE $PORT

# -------------------------------
# Iniciar Laravel con PHP Built-in server
# -------------------------------
CMD ["php", "-S", "0.0.0.0:10000", "-t", "public"]
