# syntax=docker/dockerfile:1

# ---------------------------------------------------------------------------
# Stage 1: install PHP dependencies without dev packages
# ---------------------------------------------------------------------------
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-interaction

COPY . .
RUN composer dump-autoload --no-dev --optimize --no-interaction

# ---------------------------------------------------------------------------
# Stage 2: runtime image
# ---------------------------------------------------------------------------
FROM php:8.3-apache

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN apt-get update \
    && apt-get install -y --no-install-recommends libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite opcache \
    && a2enmod rewrite headers expires \
    && rm -rf /var/lib/apt/lists/*

# Serve /public instead of the application root.
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
        /etc/apache2/sites-available/*.conf \
        /etc/apache2/apache2.conf \
        /etc/apache2/conf-available/*.conf

COPY deploy/apache-php.ini /usr/local/etc/php/conf.d/zz-emplowise.ini

WORKDIR /var/www/html

COPY --from=vendor /app /var/www/html

COPY deploy/entrypoint.sh /usr/local/bin/emplowise-entrypoint
RUN chmod +x /usr/local/bin/emplowise-entrypoint \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache database \
    && touch database/database.sqlite \
    && chown -R www-data:www-data storage bootstrap/cache database

EXPOSE 80

ENTRYPOINT ["emplowise-entrypoint"]
CMD ["apache2-foreground"]
