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

# Install PHP extensions (only the ones that need to be compiled)
RUN docker-php-ext-install \
    pdo_pgsql \
    zip \
    sockets

# Extensions are already built-in and loaded, no need for ini files

# Install pnpm
RUN npm install -g pnpm

# Copy composer files and install dependencies
COPY composer.json composer.lock ./

# Debug: Check what Composer sees in FPM
RUN composer show --platform || true

RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs

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
