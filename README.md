# Laravel + Vue.js + AdminLTE Project

## Overview
This project is a web application built with Laravel and Vue.js, using the AdminLTE template for the user interface.

## Features
- User Authentication
- CRUD product
- VIDEO Player with videojs - https://docs.videojs.com/module-browser

## Installation
Follow these steps to set up the project locally:

### Clone the Repository
```bash
git clone git@github.com:azumawanaka/test-product.git

### Install Dependencies
```bash
cd test-product
composer install

### Migrate Tables
php artisan migrate

###  Creates a symbolic link
php artisan storage:link

### Run Seeders
php artisan db:seed --class=DatabaseSeeder
php artisan db:seed --class=ProductSeeder

### Serve the application
php artisan serve

### Install Dependencies
```bash
cd test-product
npm install

### Compile Assets
npm run dev

### Now access localhost
