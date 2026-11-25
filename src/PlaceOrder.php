<?php

declare(strict_types=1);

namespace LaravelExamples\DatabaseTransactions;

use Illuminate\Database\Connection;

final class PlaceOrder
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function place(string $reference, string $sku): int
    {
        return $this->connection->transaction(function () use ($reference, $sku): int {
            $orderId = $this->connection->table('orders')->insertGetId([
                'reference' => $reference,
            ]);

            $this->connection->table('order_items')->insert([
                'order_id' => $orderId,
                'sku' => $sku,
            ]);

            return $orderId;
        });
    }
}
