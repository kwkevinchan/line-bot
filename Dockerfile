FROM composer as vender

ADD ./ /app
WORKDIR /app/

RUN composer install \
    --no-scripts \
    --no-progress \
    --no-suggest \
    --ignore-platform-reqs

FROM php:fpm-alpine as php

RUN apk add --no-cache shadow
RUN usermod -u 1000 www-data

ENV USE_CP=false \
    SERVICE_PATH=/var/www/line-bot
WORKDIR /

COPY --from=vender /app /app/

RUN cp /app/docker-entrypoint.sh /sbin/docker-entrypoint.sh
RUN chmod 755 /sbin/docker-entrypoint.sh

ENTRYPOINT [ "/sbin/docker-entrypoint.sh" ]
CMD php-fpm
