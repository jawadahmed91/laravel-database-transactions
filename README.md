# Laravel Database Transactions

Keep related Laravel database writes atomic. This example uses Laravel 13 and PHP 8.3 or later.

## Try it

```sh
composer install
composer test
```

Create `orders` and `order_items` tables with the columns shown in the test. In your application, call `(new PlaceOrder(DB::connection()))->place('ORDER-1', 'NOTEBOOK')`. The example tests both successful writes and a rollback caused by a failing database constraint. Use PHP's `pdo_sqlite` extension to run the tests.

## Add this to an existing Laravel project

1. Create migrations for the related records that must succeed or fail together.

```sh
php artisan make:migration create_orders_table
php artisan make:migration create_order_items_table
```

2. Add an `orders` primary key and reference, then add `order_id`, `sku`, and a foreign key to `order_items`. Run `php artisan migrate` after reviewing the migrations.
3. Copy `src/PlaceOrder.php` to `app/Actions/PlaceOrder.php`.
4. Change its namespace from `LaravelExamples\DatabaseTransactions` to `App\Actions`.
5. Inject the action into your controller. Laravel resolves the database connection automatically.

```php
use App\Actions\PlaceOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

public function store(Request $request, PlaceOrder $placeOrder): JsonResponse
{
    $validated = $request->validate([
        'reference' => ['required', 'string', 'max:100'],
        'sku' => ['required', 'string', 'max:100'],
    ]);

    $orderId = $placeOrder->place($validated['reference'], $validated['sku']);

    return response()->json(['order_id' => $orderId], 201);
}
```

6. Register the controller route in `routes/api.php` and submit a valid order.
7. Add every related database write to the callback passed to `transaction()`. If any write throws an exception, Laravel rolls back all earlier writes in that callback.
8. Keep external API calls, emails, and queue dispatches outside the transaction when possible. For work that must happen after a successful commit, use Laravel's after-commit queue or event support.
9. Copy `tests/ExampleTest.php` into your test suite, adapt the schema to your migrations, and run `php artisan test`.
10. Add one success test and one forced-failure test. The failure test should confirm that none of the related rows remain after rollback.

Read the [Laravel documentation](https://laravel.com/docs/13.x/database#database-transactions).
