# Base image
FROM php:8.1-fpm-alpine

RUN apk update && \
    apk add ldb-dev libldap openldap-dev
RUN docker-php-ext-install php81-pdo php81-pdo_mysql php81-mysqli php81-ldap
RUN echo "extension=ldap" >> /usr/local/etc/php/php.ini
