# ============================================
# 🎯 ÉTAPE 1 : Image de CONSTRUCTION (Node.js) 
# Cette étape installe les dépendances Node.js et compile les assets.
# ============================================
FROM node:20-alpine as builder

# Définir le répertoire de travail
WORKDIR /app

# Copier les fichiers nécessaires pour l'installation et la construction
COPY package.json package-lock.json ./
COPY vite.config.js tailwind.config.js ./

# Installer les dépendances Node.js
RUN npm install

# Copier le reste du code source
COPY . .

# Compiler les assets pour la production (crée /public/build)
# Assurez-vous que votre package.json contient bien la commande 'build'
RUN npm run build


# ============================================
# ⚙️ ÉTAPE 2 : Image de PRODUCTION (PHP)
# L'image finale, légère, avec seulement ce qui est nécessaire pour l'exécution.
# ============================================
FROM php:8.2-fpm-alpine

# ============================================
# Installer les dépendances système et PHP
# On regroupe pour optimiser les couches
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
    oniguruma-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install \
        gd \
        pdo \
        pdo_pgsql \
        bcmath

# ============================================
# Installer Composer
# ============================================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ============================================
# Configurer le répertoire de travail
# ============================================
WORKDIR /app

# ============================================
# Copier les fichiers du projet & Assets compilés
# ============================================
# Copier le code source de l'hôte
COPY . .

# Copier les assets compilés depuis l'image 'builder'
# C'est l'étape CRUCIALE qui résout votre problème de Vite
COPY --from=builder /app/public/build /app/public/build
COPY --from=builder /app/node_modules /app/node_modules
# J'ai ajouté node_modules pour les cas où des binaires sont utilisés (bien que non strictement nécessaire pour l'exécution de Laravel)


# ============================================
# Créer les dossiers et définir les permissions
# On le fait APRÈS le COPY pour que les dossiers existent
# ============================================
RUN mkdir -p bootstrap/cache storage \
 && chown -R www-data:www-data /app \
 && chmod -R 775 bootstrap/cache storage

# ============================================
# Installer les dépendances PHP
# ============================================
RUN composer install --no-dev --optimize-autoloader

# ============================================
# Commande de démarrage
# ============================================
EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]