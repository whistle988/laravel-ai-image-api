# AI Image Prompt Generator API (Laravel)

Backend API на Laravel:

## Features
- Auth (login)
- Upload image
- Generate prompt via AI (Gemini)
- Store results in DB
- Get user generations list
- Feature tests

## Endpoints
POST /api/login
POST /api/v1/prompt-generations
GET /api/v1/prompt-generations

## Setup
```bash
git clone <repo>
cd project
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
