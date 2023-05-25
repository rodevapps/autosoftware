FROM composer:2.5 as composer
FROM php:8.2.6-fpm-alpine as base

WORKDIR /code

# Symfony CLI tool
RUN apk add --no-cache bash && \
    curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash && \
    apk add symfony-cli && \
    apk del bash

# Composer
COPY --from=composer /usr/bin/composer /usr/local/bin/composer

# Clean up image
RUN rm -rf /tmp/* /var/cache

CMD ["symfony","server:start"]
