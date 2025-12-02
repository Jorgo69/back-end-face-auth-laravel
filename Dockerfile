# ============================================
# ÉTAPE 1 : Image de base PHP 8.2
# ============================================
FROM php:8.2-fpm-alpine

# ============================================
# ÉTAPE 2 : Installer les dépendances système
# ============================================
RUN apk add --no-cache \
    curl \
    git \
    zip \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libwebp-dev \
    postgresql-dev \
    oniguruma-dev

# Dépendances pour PostgreSQL
RUN apk add --no-cache postgresql-dev

# Dépendances pour GD (image processing)
RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev

# ============================================
# ÉTAPE 3 : Installer les extensions PHP
# ============================================
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-webp \
    && docker-php-ext-install \
    gd \
    pdo \
    pdo_pgsql \
    bcmath

# ============================================
# ÉTAPE 4 : Installer Composer
# ============================================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ============================================
# ÉTAPE 5 : Configurer le répertoire de travail
# ============================================
WORKDIR /app

# ============================================
# ÉTAPE 6 : Copier les fichiers du projet
# ============================================
COPY . .


# ✅ CRÉATION DES DOSSIERS AVANT COMPOSER
RUN mkdir -p bootstrap/cache storage \
 && chown -R www-data:www-data bootstrap/cache storage \
 && chmod -R 775 bootstrap/cache storage

# ============================================
# ÉTAPE 8 : Définir les permissions
# ============================================
# RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# ============================================
# ÉTAPE 7 : Installer les dépendances PHP
# ============================================
RUN composer install --no-dev --optimize-autoloader

# ============================================
# ÉTAPE 9 : Exposer le port
# ============================================
EXPOSE 8000

# ============================================
# ÉTAPE 10 : Commande de démarrage
# ============================================
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]