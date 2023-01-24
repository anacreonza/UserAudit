# Base image
FROM php:7.3-fpm-alpine

RUN apk add ldb-dev libldap openldap-dev
RUN docker-php-ext-install pdo pdo_mysql ldap
RUN echo "extension=ldap" >> /usr/local/etc/php/php.ini
