# ============================================
# Stage 1: Build Assets (Vite) & Composer
# ============================================
FROM node:22-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# ============================================
# Stage 2: PHP Application Container (Production)
# ============================================
FROM php:8.4-apache AS runner

# Install system dependencies & PHP extensions required by Laravel & Intervention Image (including WebP support)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libwebp-dev \
    libzip-dev \
    libonig-dev \
    zip \
    unzip \
    curl \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set PHP timezone to WIB agar Carbon::now() tidak meleset 7 jam dari UTC
RUN echo "date.timezone = Asia/Jakarta" > /usr/local/etc/php/conf.d/timezone.ini

# Configure Apache: set DocumentRoot to Laravel's public dir, enable FollowSymLinks & AllowOverride
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN { \
    echo '<VirtualHost *:80>'; \
    echo '    DocumentRoot /var/www/html/public'; \
    echo '    <Directory /var/www/html/public>'; \
    echo '        Options +FollowSymLinks'; \
    echo '        AllowOverride All'; \
    echo '        Require all granted'; \
    echo '    </Directory>'; \
    echo '</VirtualHost>'; \
} > /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application source code
COPY . .

# Copy built frontend assets from Stage 1
COPY --from=frontend /app/public/build ./public/build

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set proper permissions for Laravel storage & bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

# Entrypoint: fix permission volume + buat storage symlink sebelum Apache naik
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
