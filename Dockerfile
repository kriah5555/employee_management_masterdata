FROM php:8.3-fpm-bullseye

# Install dependencies and frequently used tools
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    build-essential \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    locales \
    zip \
    unzip \
    apache2 \
    iputils-ping \
    jpegoptim optipng pngquant gifsicle \
    vim \
    git \
    curl \
    wget \
    zsh \
    libpq-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install necessary PHP extensions
RUN docker-php-ext-install -j$(nproc) \
    bcmath \
    intl \
    mbstring \
    opcache \
    pcntl \
    pdo \
    pdo_pgsql \
    pgsql \
    zip \
    gd \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure pgsql --with-pgsql=/usr/local/pgsql

# Set locale to CET (Central European Time)
RUN echo "Europe/Brussels" > /etc/timezone && \
    ln -fs /usr/share/zoneinfo/Europe/Brussels /etc/localtime && \
    dpkg-reconfigure --frontend noninteractive tzdata

# Set locale
RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && \
    locale-gen

# Copy existing app directory
COPY ./ /var/www/masterdata
WORKDIR /var/www/masterdata

# Configure non-root user.
ARG PUID=1000
ENV PUID ${PUID}
ARG PGID=1000
ENV PGID ${PGID}

RUN groupmod -o -g ${PGID} www-data && usermod -o -u ${PUID} -g www-data www-data

RUN chown -R www-data:www-data /var/www/masterdata

USER www-data

# Copy and run composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Expose port 80
EXPOSE 80

# Start PHP-FPM
CMD ["php-fpm"]
