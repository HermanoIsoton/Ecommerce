
FROM dunglas/frankenphp:php8.4-trixie


COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer


RUN apt-get update && apt-get install -y --no-install-recommends \
        libzip-dev \
    && docker-php-ext-install pdo_mysql zip bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /app


COPY . .


RUN composer validate --no-check-publish \
    && composer install --no-interaction --prefer-dist \
    && composer check-platform-reqs


EXPOSE 8000
EXPOSE 443
EXPOSE 443/udp


CMD ["frankenphp", "php-server", "--root=/app/public", "--listen=:8000"]