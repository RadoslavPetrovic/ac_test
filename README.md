# AC test

This project was build using React, Symfony, SQLite

## Installing and running frontend

Node used `v16.14.0`\
Open the `./front` directory, and run:

### `npm install --no-save`

Installs all required dependencies.\
After script is completed, run:

### `npm start`

Runs the app in the development mode.\
Open [http://localhost:3000](http://localhost:3000) to view it in the browser.

## Installing and running backend

PHP used `8.2.11`\
One the `./back` directory, and run:

### `composer install`

Installs all required dependencies.\
After script is completed, run:

### `php bin/console doctrine:database:create`

Creates SQLite database.\
After script is completed, run:

### `php bin/console doctrine:migrations:migrate`

Executes migrations to database.\
After script is completed, run:

### `php bin/console doctrine:fixtures:load`

Seeds database with initial data.\
After script is completed, run:

### `symfony server:start --no-tls`

Runs Symfony server on [http://localhost:8000](http://localhost:8000).\

**Note: Login credentials are `username: user{id}` and `password: password{id}`, for example: `user1, password1`**

## Symfony tests

To start tests run:

### `php bin/phpunit`