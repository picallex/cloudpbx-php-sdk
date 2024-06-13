FROM php:8.0.30-cli

RUN apt-get update && apt-get install -y git-core zip

RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer;
