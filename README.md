# Mallow Store — Order & Inventory Mini-System

A Laravel API for recording customer orders against a product catalog with safe stock management and queued order confirmation.

## Setup

1. `composer install`
2. Copy `.env.example` to `.env` and configure your database credentials.
3. `php artisan key:generate`
4. `php artisan migrate --seed`
5. `php artisan serve`
6. In a second terminal, start the queue worker:

```bash
php artisan queue:work
```

## Running Tests

```bash
php artisan test
```

## API Endpoints

| Method | Endpoint                               | Purpose                          |
| ------ | -------------------------------------- | -------------------------------- |
| POST   | `/api/customers`                       | Create a customer                |
| POST   | `/api/products`                        | Create a product                 |
| POST   | `/api/orders`                          | Create an order                  |
| GET    | `/api/customers/{email}/orders`        | Order history for a customer     |
| GET    | `/api/products/low-stock?threshold=10` | Products below a stock threshold |

### Create Order Request

```json
{
  "customer_email": "jane@example.com",
  "customer_name": "Jane Doe",
  "items": [
    {
      "product_id": 1,
      "quantity": 2
    },
    {
      "product_id": 3,
      "quantity": 1
    }
  ]
}
```

## Assumptions & Design Decisions

* **Customer resolution:** The order endpoint accepts `customer_email` as required and `customer_name` as optional. The customer is looked up by email. If the email does not exist, a new customer is created using the supplied name, falling back to the email when a name is not provided.

* **Stock safety:** Product rows are locked using `lockForUpdate()` inside a database transaction during order creation. This prevents concurrent requests from overselling the same stock.

* **Insufficient stock:** A dedicated `InsufficientStockException` is thrown when there is not enough stock. It is returned as a `422` JSON response, and the transaction is rolled back so no partial order or stock deduction remains.

* **Queued confirmation email:** `SendOrderConfirmationJob` is dispatched after a successful order transaction. The application uses Laravel's `log` mail driver, so no real SMTP configuration is required. The generated email can be checked in `storage/logs/laravel.log`.

* **Low-stock threshold:** The low-stock API accepts a configurable `threshold` query parameter and defaults to `10`.

* **Order calculations:** Subtotal and tax are calculated from the product price, tax percentage, and requested quantity. Order items store the unit price and tax percentage used when the order was created.

* **No UI:** The task's wireframe is treated as a layout reference. This submission focuses on the required API, database logic, queue, stock safety, and automated tests.

## AI-Assisted Development

AI assistance was used during development for planning, implementation guidance, debugging, and code review.

Prompt Log screenshots are included in the "prompt log screenshots" directory.
