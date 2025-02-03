# Use official PHP CLI image
FROM php:8.2-cli

# Install necessary system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    zlib1g-dev \
    && docker-php-ext-install pdo_mysql intl zip

# Install Composer from the official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Define working directory
WORKDIR /var/www/html

# Copy dependencies configuration
COPY composer.json composer.lock ./
RUN mkdir -p bin && touch bin/console && chmod +x bin/console

ENV COMPOSER_ALLOW_SUPERUSER=1
# Install dependencies as root
RUN composer install --no-interaction --optimize-autoloader

# Copy the rest of the project files
COPY . .

# ✅ Create a non-root user
RUN useradd -m -d /var/www/html -s /bin/bash appuser && \
    chown -R appuser:appuser /var/www/html

# ✅ Switch to non-root user
USER appuser

# Expose port 8000
EXPOSE 8000

# Default command
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
