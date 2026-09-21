FROM php:7.3-cli

# php:7.3 rides an EOL Debian whose packages moved to archive.debian.org (its
# Release file is expired, and the -security repo isn't archived): repoint apt,
# drop the security line, and skip the Valid-Until check.
RUN sed -i 's|deb.debian.org|archive.debian.org|g; /security/d' /etc/apt/sources.list \
    && apt-get -o Acquire::Check-Valid-Until=false update \
    && apt-get install -y git-core zip

RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer;
