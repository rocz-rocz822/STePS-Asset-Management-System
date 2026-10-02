FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev libicu-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && apt-get install -y nodejs

WORKDIR /var/www

COPY . .

RUN composer install --optimize-autoloader --no-dev
RUN npm install && npm run build

RUN php artisan config:clear

EXPOSE 8080

CMD php artisan migrate --force && php artisan db:seed --class=AdminUserSeeder --force && php artisan config:cache && php artisan route:cache && php artisan storage:link && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}