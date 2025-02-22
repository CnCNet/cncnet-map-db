#  Stage phpbuilder
# Install php extensions and composer dependencies
FROM dunglas/frankenphp:php8.4 AS phpbuilder
LABEL authors="rohsyl"
ARG USER=www-data
ENV SERVER_NAME="http://"
RUN install-php-extensions \
 pcntl pdo_mysql curl yaml \
 gd mbstring \
 intl xml bcmath \
 zip intl readline redis sockets && \
 pecl install excimer && \
 mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN useradd -D -d /app ${USER} && \
    setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp; \
    chown -R ${USER}:${USER} /data/caddy /config/caddy /app && \
    chmod 775 /app
WORKDIR /app
USER ${USER}
COPY --chown=${USER}:${USER} composer.json composer.lock ./
RUN composer install --no-autoloader
COPY --chown=${USER}:${USER} . .
RUN mkdir -p storage/framework/sessions && \
    mkdir -p storage/framework/cache && \
    mkdir -p storage/framework/views && \
    mkdir -p storage/framework/cache/data && \
    mkdir -p storage/logs && \
    chmod -R 775 storage/logs && \
    chmod -R 775 storage/framework

# Stage : app
# Build the app with needed composer dependencies, add assets dependencies
# and run laravel octane to serve the ap
FROM phpbuilder AS app
RUN composer dump-autoload -o && \
    php artisan optimize
EXPOSE 8000
ENTRYPOINT ["php", "/app/artisan", "octane:frankenphp"]
HEALTHCHECK --start-period=5s --interval=10s --timeout=5s --retries=8 CMD php /app/artisan octane:status || exit 1
