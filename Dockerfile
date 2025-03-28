FROM scottsmith/php:8.4-fpm-alpine3.20

ARG USER_ID=1000

RUN usermod -u $USER_ID www-data

# copy configuration files
RUN rm /etc/nginx/http.d/default.conf
COPY docker/nginx-app.conf /etc/nginx/http.d/app.conf
COPY docker/entrypoint /usr/local/bin/entrypoint
COPY docker/php-fpm-docker.conf /usr/local/etc/php-fpm.d/docker.conf
RUN rm /usr/local/etc/php-fpm.d/zz-docker.conf

# php run directory
RUN mkdir /var/run/php
RUN chown www-data:www-data /var/run/php

# setup health directory
RUN mkdir /var/www/health
RUN echo "OK" > /var/www/health/index.html
RUN chown -R www-data:www-data /var/www/health

# create application directory
RUN mkdir -p /var/www/app

WORKDIR /var/www/app

EXPOSE 80

ENTRYPOINT [ "/usr/local/bin/entrypoint" ]
