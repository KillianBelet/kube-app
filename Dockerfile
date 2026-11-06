# ---- Étape 1 : dépendances PHP (Composer) ----
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist \
    --ignore-platform-reqs

# ---- Étape 2 : image finale (sans Composer, git ni dépendances de dev) ----
FROM php:8.2.8-apache

RUN docker-php-ext-install pdo_mysql \
 && a2enmod rewrite

# Un utilisateur non-root ne peut pas écouter sur le port 80 : Apache passe sur 8080
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf \
 && sed -i 's/:80>/:8080>/' /etc/apache2/sites-available/000-default.conf \
 && sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html
COPY . .
COPY --from=vendor /app/vendor ./vendor
# Ces dossiers vides ne sont pas dans Git, Laravel en a besoin pour démarrer
RUN mkdir -p bootstrap/cache storage/logs storage/framework/cache/data \
             storage/framework/sessions storage/framework/views \
 && php artisan package:discover --ansi \
 && chown -R www-data:www-data storage bootstrap/cache

# UID numérique (www-data = 33) : Kubernetes en a besoin pour vérifier runAsNonRoot
USER 33:33
EXPOSE 8080
