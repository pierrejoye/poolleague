FROM php:5.6-fpm

#RUN apt-get update -y && apt-get install -y git-core

RUN docker-php-ext-install pdo_mysql mbstring

RUN echo 'date.timezone = Asia/Bangkok' > /usr/local/etc/php/conf.d/date.ini

VOLUME /var/www/
WORKDIR /var/www/

EXPOSE 9000

# Run PHP-FPM on container start.
CMD ["php-fpm","-F"]
