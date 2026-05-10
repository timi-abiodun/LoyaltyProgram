# Loyalty Program API
A backend-only Laravel API implementing a points, achievements, badges, and cashback system using Laravel Sanctum for authentication.

---

## Live URL
**https://loyaltyprogram-f03n.onrender.com**

> Note: The app is hosted on Render's free tier and may take ~30 seconds to wake up after a period of inactivity.

---

## Setup
**Prerequisites:** PHP 8.3+, Composer, a configured database in `.env`
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan db:seed
php artisan serve
```

---

## Endpoints
| Method | Endpoint | Auth Required |
|--------|----------|---------------|
| POST | `/api/v1/register` | No |
| POST | `/api/v1/login` | No |
| POST | `/api/v1/logout` | Yes |
| POST | `/api/v1/purchases` | Yes |
| GET | `/api/v1/users/{user}/achievements` | Yes |

All protected endpoints require `Authorization: Bearer <token>`.

---

## Testing
```bash
php artisan test
```

---

## Design Decisions
- **Strategy pattern for achievement evaluation** — `PurchaseCountStrategy` and `AmountSpentStrategy` implement a shared interface, making it straightforward to add new achievement types without touching existing logic.
- **Event-driven unlocking** — purchases dispatch a `PurchaseCompleted` event; a listener runs `ProcessAchievementsService` asynchronously, keeping the purchase endpoint response fast.
- **Rate limiting** — auth endpoints are limited to 5 req/min; purchase endpoints to 3 req/min, configured in `AppServiceProvider` to reduce double-spend risk.
- **Cashback is mocked** — badge unlock triggers a log entry rather than a real payment provider call, as per spec.