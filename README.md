# Mallow Store — Order & Inventory Mini-System

A Laravel API for recording customer orders against a product catalog with real-time stock sync.

## Setup

1. `composer install`
2. `cp .env.example .env` and set your DB credentials (SQLite works out of the box — `database/database.sqlite` is already included)
3. `php artisan key:generate`
4. `php artisan migrate --seed`
5. `php artisan serve`
6. In a second terminal, start the queue worker: `php artisan queue:work`

## Running tests

php artisan test

## API Endpoints

| Method | Endpoint | Purpose |
|---|---|---|
| POST | `/api/customers` | Create a customer |
| POST | `/api/products` | Create a product |
| POST | `/api/orders` | Create an order (see body below) |
| GET | `/api/customers/{email}/orders` | Order history for a customer |
| GET | `/api/products/low-stock?threshold=10` | Products below a stock threshold (default 10) |

**Create order request body:**
```json
{
  "customer_email": "jane@example.com",
  "customer_name": "Jane Doe",
  "items": [
    { "product_id": 1, "quantity": 2 },
    { "product_id": 3, "quantity": 1 }
  ]
}
```

## Assumptions & design decisions

- **Customer resolution**: the order endpoint takes `customer_email` (required) and `customer_name` (optional). The customer is looked up by email; if none exists, a new one is created using `customer_name`, falling back to the email itself if no name is given. This matches the wireframe's "auto-filled if email exists" behavior.
- **Stock safety**: `Product` rows are locked with `lockForUpdate()` inside a DB transaction during order creation, so concurrent requests for the last unit of stock serialize at the database level — exactly one succeeds, the other fails cleanly with a `422` and no row is left oversold.
- **Insufficient stock** throws a dedicated `InsufficientStockException`, mapped in `bootstrap/app.php` to a `422 { "message": "..." }` response rather than a raw 500.
- **Queued confirmation email**: `SendOrderConfirmationJob` is dispatched (not called directly) after the order transaction commits, and uses `MAIL_MAILER=log` so no real SMTP is needed — check `storage/logs/laravel.log` after placing an order.
- **Low-stock threshold** is a query parameter (`?threshold=`) defaulting to 10, rather than a hardcoded value, per requirement 4.
- No UI was built — the brief's wireframe is a layout reference only; this submission is API + tests as scoped.

## AI-assisted development

Built with Claude's help for planning the schema, the locking strategy, and code review. See `/prompts` for the chat screenshots.