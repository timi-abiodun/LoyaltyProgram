# Loyalty Program (Laravel)

A small loyalty-program API + dashboard UI.

## What this app does
- User authentication (register/login/logout) using **Laravel Sanctum tokens**.
- Users can create purchases.
- Purchases unlock **achievements** (by purchase count and amount spent).
- Achievement unlocks can award **badges**.
- Badge unlocks can award **cashback**.
- A protected user endpoint returns dashboard data (achievements + next badge state).

## Tech stack
- PHP 8.3+
- Laravel 13
- Sanctum (token auth)
- Vite + TailwindCSS (front-end build)
- Pest (tests)

## Requirements
- PHP 8.3+
- Composer
- Node.js + npm
- A configured database (see `.env`)

## Local setup

1) Install PHP dependencies
```bash
composer install
```

2) Copy env + generate key
```bash
cp .env.example .env
php artisan key:generate
```

3) Configure DB in `.env`

4) Run migrations
```bash
php artisan migrate --force
```

5) (Optional) Seed demo data
```bash
php artisan db:seed --class=DatabaseSeeder
```

6) Install JS dependencies + build/dev assets
```bash
npm install --ignore-scripts
npm run dev
```

## Run the server
```bash
php artisan serve
```

If you want the full dev workflow (web + queue worker + Vite) use:
```bash
composer run dev
```

## API (v1)
Base path: `/api/v1`

### Auth
#### Register
`POST /api/v1/register`
- Creates a user and returns:
  - `access_token` (Bearer token)
  - `token_type` (`Bearer`)

#### Login
`POST /api/v1/login`
- Accepts `username` + `password`
- Deletes existing user tokens (single active session behavior)
- Returns a new `access_token`.

#### Logout
`POST /api/v1/logout`
- Protected by `auth:sanctum`
- Revokes the current access token.

### Purchases
#### Create purchase
`POST /api/v1/purchases`
- Protected by `auth:sanctum`
- Rate limited by `throttle:purchases` (3 req/min)
- Body: JSON
  - `amount` (number)

Response: `201 Created` with:
- `{ "message": "Purchase completed successfully." }`

This triggers achievement/badge/cashback processing via events & listeners.

### User dashboard data
#### Get dashboard achievements/badges
`GET /api/v1/users/{user}/achievements`
- Protected by `auth:sanctum`
- Returns data consumed by the dashboard UI.

## Configuration
Cashback amount is controlled via:
- `config/loyalty.php` (`cashback_amount`, default: `300`)


## Testing
Run all tests:
```bash
php artisan test
```

## Notes / current TODO
There is an ongoing modernization task tracked in `TODO.md`.


