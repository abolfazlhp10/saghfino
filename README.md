# Saghfino (سقفینو)

Back end for Saghfino, a real estate listings website where people can post, search and save property ads for buying, selling and renting in any city in Iran.

This repository is the Laravel REST API. The front end is a separate React + TypeScript app.

- Live demo: https://saghfinoo-five.vercel.app (a VPN may be needed to load it)
- API documentation (Swagger): https://saghfino.abolfazlhp.ir/docs
- UI design (Figma): https://www.figma.com/community/file/1332686061667584715/saghfinoo-real-estate-website

## Features

**Sign-in**
- Phone number login with a one-time code sent by SMS (FarazSMS), with rate limiting on sending and verifying codes
- Token authentication with Laravel Sanctum
- Three roles managed with spatie/laravel-permission: user, real estate agent and admin

**Ads**
- Post an ad with images, transaction type (sell, rent, mortgage), property type, prices and city
- Search with pagination and filters: city, transaction and property type, price range, area, rooms, floor, elevator, parking and title, with sorting by price
- Similar ads on each ad page
- Save ads to a favorites list, and manage your own ads (view, delete one or all)
- Only active ads are listed publicly (global query scope)

**Real estate agencies**
- Agents can register an agency with name, address, slogan and logo, and edit its profile
- Public pages for agencies and agents
- Users can rate agencies

**Admin panel API**
- Counts of users and ads for the dashboard charts
- Lists of users, agents, agencies and ads
- Activate, deactivate or delete ads

## Tech stack

- PHP 8.2, Laravel 11
- MySQL
- Laravel Sanctum, spatie/laravel-permission
- morilog/jalali for Persian (Jalali) dates
- FarazSMS for OTP messages
- Swagger (OpenAPI) for API docs

## Project structure

```
app/
  Http/Controllers/Auth    OTP login and registration
  Http/Controllers/Core    Ads and real estate agencies
  Http/Controllers/Admin   Admin panel endpoints
  Services/                Business logic for ads and agencies
  Actions/                 Single-purpose actions (e.g. creating a user with a role)
  Models/Scopes/           Global scope that hides inactive ads
  SMS/                     FarazSMS client
routes/api/                auth.php, ads.php, real_estate.php, admin.php
public/swagger.yaml        API specification
```

## Running locally

Requirements: PHP 8.2+, Composer, MySQL.

```bash
git clone https://github.com/abolfazlhp10/saghfino.git
cd saghfino
composer install
cp .env.example .env
php artisan key:generate
```

Set your database in `.env`, plus these values:

```env
ADMIN_USER=
ADMIN_PASS=
ADMIN_PHONENUMBER=

FARAZSMS_USERNAME=
FARAZSMS_PASSWORD=
FARAZSMS_FROM=
FARAZSMS_PATTERN_CODE=
```

Then create the tables and seed provinces, cities, roles, the admin user and sample ads:

```bash
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

The API is served at `http://localhost:8000/api`, and the docs at `http://localhost:8000/docs`.
