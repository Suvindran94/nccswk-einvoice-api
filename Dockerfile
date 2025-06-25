FROM php:8.4.5

# Install system dependencies
RUN apt-get update -y && apt-get install -y --no-install-recommends \
    openssl \
    zip \
    unzip \
    git \
    vim \
    curl \
    libmariadb-dev-compat \
    sudo \
    libonig-dev && \
    rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Create dev user with correct permissions
RUN useradd -ms /bin/bash -u 1000 -G www-data,sudo dev && \
    echo "dev ALL=(ALL) NOPASSWD:ALL" > /etc/sudoers.d/dev-user && \
    chmod 0440 /etc/sudoers.d/dev-user

# Set working directory
WORKDIR /app

# Create necessary directories and set permissions
RUN mkdir -p /app/vendor && \
    chown -R dev:www-data /app && \
    chmod -R ug+rwX /app

EXPOSE 8000

USER dev

CMD php artisan serve --host=0.0.0.0 --port=8000