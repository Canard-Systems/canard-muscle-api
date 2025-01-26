# Utiliser une image PHP officielle
FROM php:8.2-cli

# Installer les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    zlib1g-dev \
    && docker-php-ext-install pdo_mysql intl zip

# Installer Composer depuis l'image officielle
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier le fichier composer.json et installer les dépendances
COPY composer.json composer.lock ./
RUN composer install --no-interaction --optimize-autoloader

# Copier le reste des fichiers du projet
COPY . .

# Exposer le port pour le serveur PHP
EXPOSE 8000

# Commande par défaut (inutile si command est défini dans docker-compose.yml)
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
