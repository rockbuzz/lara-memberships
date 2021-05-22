FROM php:7.2-cli-alpine

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ADD entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT [ "/entrypoint.sh" ]