<?php

namespace App\Adapters;

use App\Contracts\CartInterface;
use Surfsidemedia\Shoppingcart\Facades\Cart;
use Illuminate\Support\Collection;

class CartAdapter implements CartInterface
{
    public function getItems(): Collection
    {
        return Cart::instance('cart')->content();
    }

    public function addItem(int $id, string $name, int $quantity, float $price, array $options = []): void
    {
        Cart::instance('cart')->add($id, $name, $quantity, $price, $options)
            ->associate('App\Models\Product');
    }

    public function updateItem(string $rowId, int $quantity): void
    {
        Cart::instance('cart')->update($rowId, $quantity);
    }

    public function removeItem(string $rowId): void
    {
        Cart::instance('cart')->remove($rowId);
    }

    public function clear(): void
    {
        Cart::instance('cart')->destroy();
    }

    public function count(): int
    {
        return Cart::instance('cart')->count();
    }

    public function subtotal(): float
    {
        return floatval(str_replace(',', '', Cart::instance('cart')->subtotal()));
    }

    public function total(): float
    {
        return floatval(str_replace(',', '', Cart::instance('cart')->total()));
    }

    public function tax(): float
    {
        return floatval(str_replace(',', '', Cart::instance('cart')->tax()));
    }
}

/*
latest commit
    */
