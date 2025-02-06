<?php

namespace App\Services\Discounts;

use App\Models\Order;

class CategoryCheapestDiscount implements DiscountInterface
{
    private int $categoryId;
    private int $minItems;
    private float $discountRate;

    public function __construct(int $categoryId = 1, int $minItems = 2, float $discountRate = 0.20)
    {
        $this->categoryId = $categoryId;
        $this->minItems = $minItems;
        $this->discountRate = $discountRate;
    }

    public function apply(Order $order, float &$currentTotal): array
    {
        $categoryItems = [];

        foreach ($order->items as $item) {
            if ($item->product->category === $this->categoryId) {
                $categoryItems[] = $item;
            }
        }

        if (count($categoryItems) >= $this->minItems) {
            usort($categoryItems, fn($a, $b) => $a->product->price <=> $b->product->price);
            $discount = round($categoryItems[0]->product->price * $this->discountRate, 2);
            $currentTotal = round( $currentTotal - $discount, 2);

            return [
                'discountReason' => 'CHEAPEST_' . $this->minItems . '_ITEMS_' . ($this->discountRate * 100) . '_PERCENT',
                'discountAmount' => $discount,
                'subtotal' => $currentTotal
            ];
        }

        return [];
    }
}
