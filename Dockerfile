FROM php:8.2-fpm

# Get frequently used tools
RUN apt-get update && apt-get install -y \
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
  libpq-dev

RUN docker-php-ext-configure pgsql --with-pgsql=/usr/local/pgsql \
  && docker-php-ext-install pdo pdo_pgsql pgsql

RUN docker-php-ext-configure zip

RUN docker-php-ext-install \
  bcmath \
  mbstring \
  pcntl \
  intl \
  zip \
  opcache \
  pgsql

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy existing app directory
COPY ./ /var/www/identity_manager
WORKDIR /var/www/identity_manager


# Configure non-root user.
ARG PUID=1000
ENV PUID ${PUID}
ARG PGID=1000
ENV PGID ${PGID}

RUN groupmod -o -g ${PGID} www-data && usermod -o -u ${PUID} -g www-data www-data

RUN chown -R www-data:www-data /var/www/identity_manager

USER www-data

# Copy and run composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN composer install --no-interaction

# For Laravel Installations
#RUN php artisan key:generate

EXPOSE 80

CMD ["php-fpm"]