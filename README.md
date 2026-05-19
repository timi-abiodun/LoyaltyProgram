# Loyalty Program API

A backend-only Laravel API implementing a points, achievements, badges, and cashback system using Laravel Sanctum for authentication.

**Live URL:** `https://loyaltyprogram-f03n.onrender.com`

> ⚠️ The app is hosted on Render's free tier and may take ~30 seconds to wake up after a period of inactivity.

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

## Authentication

All protected endpoints require a Bearer token in the `Authorization` header. Obtain the token from `/register` or `/login`.

```
Authorization: Bearer <your_access_token>
Content-Type: application/json
```

---

## Endpoints

| Method | Endpoint | Auth Required |
|--------|----------|---------------|
| POST | /api/v1/register | No |
| POST | /api/v1/login | No |
| POST | /api/v1/logout | Yes |
| GET | /api/v1/user | Yes |
| POST | /api/v1/purchases | Yes |
| GET | /api/v1/users/{user}/achievements | Yes |

---

### `POST /api/v1/register`

**Request:**
```json
{
    "full_name": "Red Hood",
    "username": "red",
    "email": "red@gmail.com",
    "password": "1234test",
    "password_confirmation": "1234test"
}
```

**Response `201`:**
```json
{
    "user": {
        "id": "019dbf83-8b38-717a-85ff-e79c77c4e340",
        "full_name": "Red Hood",
        "username": "red",
        "email": "red@gmail.com",
        "updated_at": "2026-04-25T14:35:37.000000Z",
        "created_at": "2026-04-24T12:42:42.000000Z"
    },
    "access_token": "<token>",
    "token_type": "Bearer"
}
```

---

### `POST /api/v1/login`

Deletes existing user tokens — only one active session at a time.

**Request:**
```json
{
    "username": "red",
    "password": "1234test"
}
```

**Response `200`:**
```json
{
    "access_token": "<token>",
    "token_type": "Bearer"
}
```

---

### `POST /api/v1/logout`

**Response `200`:**
```json
{
    "message": "Logged out successfully"
}
```

---

### `GET /api/v1/user`

Returns the authenticated user's profile and running stats.

**Response `200`:**
```json
{
    "id": "019dbf83-8b38-717a-85ff-e79c77c4e340",
    "full_name": "Red Hood",
    "username": "red",
    "email": "red@gmail.com",
    "current_points": 1050,
    "total_amount_spent": "50000.00",
    "total_purchase_count": 5,
    "created_at": "2026-04-24T12:42:42.000000Z",
    "updated_at": "2026-04-25T14:35:37.000000Z"
}
```

---

### `POST /api/v1/purchases`

Rate limited to **3 requests/minute**. Triggers achievement, badge, and cashback processing asynchronously — the `201` is returned immediately, so there may be a short delay before `/achievements` reflects a new unlock.

**Request:**
```json
{
    "amount": 1234
}
```

**Response `201`:**
```json
{
    "message": "Purchase completed successfully."
}
```

---

### `GET /api/v1/users/{user}/achievements`

Replace `{user}` with the user's UUID from the auth response.

**Response `200`:**
```json
{
    "data": {
        "achievements": {
            "unlocked_achievements": [
                {
                    "id": "019dbf83-13fc-72e5-af3d-ef3e608edc84",
                    "name": "Whale",
                    "type": "amount_spent",
                    "points_awarded": 1000,
                    "threshold": 25000,
                    "created_at": "2026-04-24T12:42:11.000000Z",
                    "updated_at": "2026-04-24T12:42:11.000000Z"
                }
            ],
            "next_available_achievements": [
                {
                    "id": "019dbf83-12b9-7036-a3ae-6b153499f78a",
                    "name": "Early Bird",
                    "type": "purchases_count",
                    "points_awarded": 10,
                    "threshold": 1,
                    "created_at": "2026-04-24T12:42:11.000000Z",
                    "updated_at": "2026-04-24T12:42:11.000000Z"
                }
            ]
        },
        "badges": {
            "current_badge": {
                "id": "019dbf83-12c6-7236-8446-b432a3e8711b",
                "name": "Loyal Customer",
                "points_required": 1000,
                "created_at": "2026-04-24T12:42:11.000000Z",
                "updated_at": "2026-04-24T12:42:11.000000Z"
            },
            "next_badge": {
                "id": "019dbf83-1532-7004-8f18-764736fe027f",
                "name": "Elite Member",
                "points_required": 2000,
                "created_at": "2026-04-24T12:42:12.000000Z",
                "updated_at": "2026-04-24T12:42:12.000000Z"
            },
            "remaining_to_unlock_next_badge": 950
        },
        "meta": {
            "generated_at": "2026-04-25T15:03:26+00:00"
        }
    }
}
```

**Field notes:**
- `unlocked_achievements` — achievements the user has already earned.
- `next_available_achievements` — achievements not yet unlocked; useful for showing progress prompts.
- `current_badge` — the user's current badge tier. `null` if no badge earned yet.
- `next_badge` — the next badge to unlock. `null` if the user is at the highest tier.
- `remaining_to_unlock_next_badge` — points still needed to reach `next_badge`.
- Achievement `type` is either `"purchases_count"` or `"amount_spent"`.

---

## Error Responses

| Status | Meaning |
|--------|---------|
| `401` | Unauthenticated — missing or invalid Bearer token |
| `403` | Forbidden — not authorized for this resource |
| `404` | Not found |
| `422` | Validation error — check the `errors` object |
| `429` | Rate limit hit — auth: 5 req/min, purchases: 3 req/min |
| `500` | Server error |

**`422` example:**
```json
{
    "message": "The email field is required.",
    "errors": {
        "email": ["The email field is required."]
    }
}
```

**`401` example:**
```json
{
    "message": "Unauthenticated."
}
```

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