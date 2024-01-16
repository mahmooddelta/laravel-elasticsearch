FROM php:8.1-fpm-alpine
RUN apk add libpng-dev git
RUN docker-php-ext-install  pdo pdo_mysql gd
RUN apk add --no-cache pcre-dev $PHPIZE_DEPS \
        && pecl install redis \
        && docker-php-ext-enable redis.so

RUN curl -s https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer
RUN alias composer='php /usr/bin/composer'
RUN chmod -R 755 /var/www
RUN chown -R www-data:www-data /var/www
