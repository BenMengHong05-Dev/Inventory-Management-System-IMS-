# Justfile for Inventory Management System

default:
    @just serve

serve:
    php -c php.ini artisan serve --host=127.0.0.1 --port=8000

migrate:
    php -c php.ini artisan migrate

seed:
    php -c php.ini artisan db:seed

setup:
    composer install
    php -c php.ini artisan key:generate
    php -c php.ini artisan migrate --seed
