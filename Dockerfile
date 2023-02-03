FROM php:7.4-alpine

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apk --update add sqlite-dev mysql-dev && \
    docker-php-ext-install pdo_sqlite mysqli pdo_mysql 

ENTRYPOINT [ "/bin/sh" ]