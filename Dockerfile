FROM php:8.2-apache

RUN docker-php-ext-install mysqli


# Enable SSL and headers modules
RUN a2enmod ssl headers

# Copy certificate and key
COPY ./certs/server.crt /etc/ssl/certs/server.crt
COPY ./certs/server.key /etc/ssl/private/server.key

# Copy custom SSL vhost
COPY ./default-ssl.conf /etc/apache2/sites-available/default-ssl.conf

# Enable SSL site
RUN a2ensite default-ssl.conf
