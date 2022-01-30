# slim-skeleton

Skeleton Slim 4 application for new projects.

This package makes it quicker to bootstrap a new Slim 4 application with common
dependencies and a local development environment using Docker. It is only
intended to be used as an initial bootstrap - after that you should commit your
changes to your own Git repository and manage your Composer dependencies.

## Installation

Create a project via Composer (do **not** use `composer require` as this will
add `phpdev-uk/slim-skeleton` as a dependency):

`composer create-project phpdev-uk/slim-skeleton:dev-main my-app`

Change any image versions in `docker-compose.yml` to match your production environment.

Copy the `.env.dist` file:

`cp .env.dist .env`

Make any changes to `.env`.

Start Docker:

`docker-compose up`

Visit [https://docker.localhost](https://docker.localhost) in your browser.

## Assumptions

This skeleton application makes the following assumptions about your application environment:

* PHP 7.4
* Single MySQL database
