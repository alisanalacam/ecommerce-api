<?php

namespace App\Services\Discounts;

use App\Models\Order;

class CategoryBulkDiscount implements DiscountInterface
{
    private int $categoryId;
    private int $bulkQuantity;
    private int $freeItems;

    public function __construct(int $categoryId = 2, int $bulkQuantity = 6, int $freeItems = 1)
    {
        $this->categoryId = $categoryId;
        $this->bulkQuantity = $bulkQuantity;
        $this->freeItems = $freeItems;
    }

    public function apply(Order $order, float &$currentTotal): array
    {
        $discount = 0;

        foreach ($order->items as $item) {
            if ($item->product->category === $this->categoryId && $item->quantity >= $this->bulkQuantity) {
                $freeCount = intdiv($item->quantity, $this->bulkQuantity) * $this->freeItems;
                $discount += $freeCount * $item->product->price;
            }
        }

        if ($discount > 0) {
            $currentTotal = round($currentTotal - $discount, 2);

            return [
                'discountReason' => 'BUY_' . $this->bulkQuantity . '_GET_'  . $this->freeItems,
                'discountAmount' => round($discount, 2),
                'subtotal' => $currentTotal
            ];
        }

        return [];
    }
}
