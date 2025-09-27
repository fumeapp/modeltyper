FROM mcr.microsoft.com/devcontainers/base:ubuntu

# PHP
RUN apt-get -y update && apt-get -y upgrade \
    && apt-get -y install software-properties-common \
    && add-apt-repository ppa:ondrej/php \
    && apt-get -y update \
    && apt-get -y install php8.2 php8.2-gd php8.2-xml php8.2-soap php8.2-mbstring php8.2-mysql php8.2-sqlite3 php8.2-dev php8.2-zip php8.2-curl

# XDEBUG Setup
RUN echo "xdebug.mode = develop,debug" >> /etc/php/8.2/cli/conf.d/20-xdebug.ini \
    && echo "xdebug.start_with_request = yes" >> /etc/php/8.2/cli/conf.d/20-xdebug.ini \
    && echo "xdebug.client_host = localhost" >> /etc/php/8.2/cli/conf.d/20-xdebug.ini \
    && echo "xdebug.discover_client_host=1" >> /etc/php/8.2/cli/conf.d/20-xdebug.ini \
    && echo "xdebug.client_port = 9003" >> /etc/php/8.2/cli/conf.d/20-xdebug.ini \
    && echo "xdebug.log = /var/log/xdebug.log" >> /etc/php/8.2/cli/conf.d/20-xdebug.ini

RUN touch /var/log/xdebug.log

# Composer
RUN cd ~
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php \
    && HASH=`curl -sS https://composer.github.io/installer.sig` \
    && php -r "if (hash_file('SHA384', 'composer-setup.php') === '$HASH') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer

# Forward Xdebug port
EXPOSE 9003
