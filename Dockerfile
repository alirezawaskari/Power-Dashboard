FROM php:8.2-fpm-alpine

WORKDIR /app

# Install system dependencies
RUN echo "https://repo.iut.ac.ir/repo/alpine/v3.22/main" > /etc/apk/repositories && \
    echo "https://repo.iut.ac.ir/repo/alpine/v3.22/community" >> /etc/apk/repositories && \
    apk update && apk add --no-cache \
    nodejs \
    npm \
    composer \
    postgresql-dev \
    libsodium-dev \
    libzip-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    linux-headers \
    bison \
    re2c

# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    zip \
    session \
    sodium \
    fileinfo \
    dom \
    sockets

# Enable all extensions manually
RUN echo "extension=pdo" > /usr/local/etc/php/conf.d/pdo.ini && \
    echo "extension=pdo_pgsql" > /usr/local/etc/php/conf.d/pdo_pgsql.ini && \
    echo "extension=zip" > /usr/local/etc/php/conf.d/zip.ini && \
    echo "extension=session" > /usr/local/etc/php/conf.d/session.ini && \
    echo "extension=sodium" > /usr/local/etc/php/conf.d/sodium.ini && \
    echo "extension=fileinfo" > /usr/local/etc/php/conf.d/fileinfo.ini && \
    echo "extension=dom" > /usr/local/etc/php/conf.d/dom.ini && \
    echo "extension=sockets" > /usr/local/etc/php/conf.d/sockets.ini && \
    echo "extension=tokenizer" > /usr/local/etc/php/conf.d/tokenizer.ini

# Install pnpm
RUN npm install -g pnpm

# Copy composer files and install dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy package files and install dependencies
COPY package.json pnpm-lock.yaml ./
RUN pnpm install --frozen-lockfile

# Copy application code
COPY . .

# Build frontend assets
RUN pnpm run build

# Set permissions
RUN chown -R www-data:www-data /app \
    && chmod -R 755 /app/storage \
    && chmod -R 755 /app/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
