<?php
namespace App\Services;

use App\Models\Product;

class StockService
{
    public function checkStock(array $items): bool
    {
        foreach ($items as $item) {
            $product = Product::find($item['productId']);
            if (!$product || $product->stock < $item['quantity']) {
                return false;
            }
        }
        return true;
    }

    public function updateStock(array $items): void
    {
        foreach ($items as $item) {
            $product = Product::find($item['productId']);
            $product->decrement('stock', $item['quantity']);
        }
    }

    public function restoreStock(array $items)
    {
        foreach ($items as $item) {
            Product::where('id', $item['productId'])->increment('stock', $item['quantity']);
        }
    }
}
