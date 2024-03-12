# Base image
FROM php:7.4-fpm-alpine

RUN apk add ldb-dev libldap openldap-dev
RUN docker-php-ext-install pdo pdo_mysql mysqli ldap 
RUN echo "extension=ldap" >> /usr/local/etc/php/php.ini
