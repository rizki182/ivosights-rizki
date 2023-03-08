FROM php:8.2.2-fpm

# Set working directory
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libpq-dev \
    libzip-dev \
    locales \
    libonig-dev \
    zip \
    jpegoptim optipng pngquant gifsicle \
    nano \
    unzip \
    curl \
    supervisor

# Install extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) gd

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

RUN curl -fsSL https://deb.nodesource.com/setup_14.x | bash -
RUN apt-get update && apt-get install nodejs
RUN npm install --global yarn

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
