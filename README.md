<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Mafia - Laravel Game
## Introduction
Mafia is an engaging and interactive party game, now brought to life through Laravel. In this game, players take on the roles of Mafia members, townsfolk, and other characters, each with their own unique abilities and objectives. The game combines strategy, social deduction, and role-playing elements, making it an exciting experience for all players.

## Features
Real-time multiplayer gameplay
User register
User login
User profiles
and more...
## Requirements
PHP >= 7.4
Laravel 8.x
Node.js
npm

## Endpoints

```bash
localhost:8000/login
localhost:8000/register
```
## Installation
1. Clone the Repository

```bash
git clone https://github.com/blini1/mafia-game.git
cd mafia-game
```

2. Install Dependencies
I have used node 16 version
```bash
composer install
php artisan breeze:install
npm install
npm run build
npm run dev
```
3. Database Setup
   Create a database for the project.
   Configure your .env file with the appropriate database credentials.
   Then, run the following commands to set up the database and seed it with initial data, including a testing user account:

```bash
php artisan migrate
php artisan db:seed
```

This will create a testing user with the following credentials:

Email: john@wolf.com
Password: 123Password!
Use these credentials to log in and test the application's features.

4.Serve the Application

```bash
php artisan serve
```

The application will be running on http://localhost:8000.

# Contact information

For any inquiries or further information, please contact me at blinbakija@gmail.com
