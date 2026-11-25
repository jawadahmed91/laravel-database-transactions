# Laravel Database Transactions

Keep related Laravel database writes atomic. This example uses Laravel 13 and PHP 8.3 or later.

## Try it

```sh
composer install
composer test
```

Create `orders` and `order_items` tables with the columns shown in the test. In your application, call `(new PlaceOrder(DB::connection()))->place('ORDER-1', 'NOTEBOOK')`. The example tests both successful writes and a rollback caused by a failing database constraint. Use PHP's `pdo_sqlite` extension to run the tests.

Read the [Laravel documentation](https://laravel.com/docs/13.x/database#database-transactions).
