<?php

declare(strict_types=1);

namespace LaravelExamples\DatabaseTransactions;

use PHPUnit\Framework\TestCase;
use Illuminate\Database\QueryException;
use Illuminate\Database\SQLiteConnection;
use PDO;

final class ExampleTest extends TestCase
{
    private SQLiteConnection $database;

    protected function setUp(): void
    {
        $this->database = new SQLiteConnection(new PDO('sqlite::memory:'));
        $this->database->statement('CREATE TABLE orders (id INTEGER PRIMARY KEY, reference TEXT NOT NULL)');
        $this->database->statement("CREATE TABLE order_items (order_id INTEGER NOT NULL, sku TEXT CHECK (sku <> ''))");
    }

    public function testItCommitsTheOrderAndItemTogether(): void
    {
        $id = (new PlaceOrder($this->database))->place('ORDER-1', 'NOTEBOOK');

        self::assertSame('ORDER-1', $this->database->table('orders')->where('id', $id)->value('reference'));
        self::assertSame('NOTEBOOK', $this->database->table('order_items')->where('order_id', $id)->value('sku'));
    }

    public function testARejectedItemRollsBackTheOrder(): void
    {
        try {
            (new PlaceOrder($this->database))->place('ORDER-2', '');
            self::fail('The database constraint should reject an empty SKU.');
        } catch (QueryException $exception) {
            self::assertSame(0, $this->database->table('orders')->count());
            self::assertSame(0, $this->database->table('order_items')->count());
        }
    }
}
