# slim-skeleton

Skeleton Slim 4 application for new projects.

This package makes it quicker to bootstrap a new Slim 4 application with common
dependencies and a local development environment using Docker. It is only
intended to be used as an initial bootstrap - after that you should commit your
changes to your own Git repository and manage your Composer dependencies.

If you are using Visual Studio Code, there are also some settings to integrate
Xdebug and to ignore third party libraries (`vendor/`) and the database file.

## Installation

Create a project via Composer (do **not** use `composer require` as this will
add `phpdev-uk/slim-skeleton` as a dependency):

`composer create-project phpdev-uk/slim-skeleton:dev-main my-app`

Set your PHP version in `composer.json`.

Change any image versions in `docker-compose.yml` to match your production environment.

Make any changes to `.env`.

Start Docker:

`docker-compose up`

Visit [https://docker.localhost](https://docker.localhost) in your browser.

## Assumptions

This skeleton application makes the following assumptions about your application environment:

* PHP 7.4
* Single MariaDB database
* Debian Bullseye (latest stable release)

## Included libraries

Although you do not have to use them, this package includes the following libraries:

 * Doctrine ORM: Create PHP classes to map to your database structure.
 * Doctrine Migrations: Manage changes to your database schemas.

If you are using a database in your application, the above libraries are likely to make
your life much easier.

## Middleware

This skeleton repository comes with an `AuthRequired` middleware class, which can be used
to place routes behind a authentication wall. You will need to either make sure that your
authentication code sets `$_SESSION['logged_in']` to `true` when the user is authenticated,
or edit the class to use your authentication process.
