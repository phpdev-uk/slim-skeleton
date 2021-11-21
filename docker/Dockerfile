FROM php:7.4-apache-buster

ARG SITE_HOST=docker.localhost
ARG SSL_CN=${SITE_HOST}
ARG PRIVATE_KEY_PATH=/etc/ssl/private/${SSL_CN}.key
ARG CERTIFICATE_PATH=/etc/ssl/certs/${SSL_CN}.pem

RUN apt-get update && apt-get upgrade -y && apt-get install -y \
        libpng-dev \
        libzip-dev

RUN docker-php-ext-install bcmath
RUN docker-php-ext-install gd
RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN docker-php-ext-install zip

RUN /usr/bin/openssl genrsa -out ${PRIVATE_KEY_PATH} 2048
RUN /usr/bin/openssl req -new -x509 -key ${PRIVATE_KEY_PATH} -out ${CERTIFICATE_PATH} -days 90 -subj /CN=${SSL_CN}

COPY apache/sites-available/${SITE_HOST}.conf /etc/apache2/sites-available/
RUN a2enmod rewrite ssl
RUN a2ensite ${SITE_HOST}
RUN service apache2 restart
EXPOSE 443
