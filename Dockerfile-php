# Container do PHP
FROM php:8.1-fpm

RUN apt-get update && apt-get install -y \
    git \
    libzip-dev \
    unzip \
    libpq-dev \
    libpng-dev \
    libmagickwand-dev \
    && docker-php-ext-install pdo pdo_mysql zip \
    && docker-php-ext-install gd \
    && docker-php-ext-enable gd \
    && pecl install imagick \
    && docker-php-ext-enable imagick

# Pasta de trabalho
WORKDIR /var/www/html

# Copia o código-fonte do Laravel para dentro do container
COPY . .

# Instala o composer
RUN curl -sS https://getcomposer.org/installer | /usr/local/bin/php -- --install-dir=/usr/local/bin --filename=composer

# Copia o arquivo .env para dentro do container
COPY .env.example .env

# Expõe a porta do servidor web
EXPOSE 9000

# Inicia o servidor web do PHP
CMD ["php-fpm"]
