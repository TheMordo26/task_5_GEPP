FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git unzip zip libicu-dev libonig-dev libxml2-dev libzip-dev libpq-dev \
    && docker-php-ext-install intl pdo pdo_pgsql zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /var/www/html/

WORKDIR /var/www/html/

RUN composer install --no-interaction --no-dev --optimize-autoloader --no-scripts \
    && composer dump-env prod --no-interaction --no-scripts -- --no-plugins=0 \
    && php bin/console cache:clear --no-warmup \
    && php bin/console cache:warmup

RUN chown -R www-data:www-data /var/www/html/ \
    && chmod -R 755 /var/www/html/

RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

EXPOSE 80