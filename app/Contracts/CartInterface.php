<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface CartInterface
{
    public function getItems(): Collection;
    public function addItem(int $id, string $name, int $quantity, float $price, array $options = []): void;
    public function updateItem(string $rowId, int $quantity): void;
    public function removeItem(string $rowId): void;
    public function clear(): void;
    public function count(): int;
    public function subtotal(): float;
    public function total(): float;
    public function tax(): float;
}
