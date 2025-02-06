<?php

namespace App\Services;

use App\Models\Order;
use App\Services\Discounts\CategoryBulkDiscount;
use App\Services\Discounts\CategoryCheapestDiscount;
use App\Services\Discounts\DiscountInterface;
use App\Services\Discounts\TotalAmountDiscount;

class DiscountService
{
    private array $discountRules = [];

    public function __construct()
    {
        $this->discountRules = [
            new TotalAmountDiscount(1000, 0.10),
            new CategoryBulkDiscount(2, 6, 1),
            new CategoryCheapestDiscount(1, 2, 0.20)
        ];
    }
    public function calculateDiscounts(int $orderId): array
    {
        $order = Order::find($orderId);

        $currentTotal = $order->total;
        $applicableDiscounts = [];

        foreach ($this->discountRules as $rule) {
            $discount = $rule->apply($order, $currentTotal);
            if ($discount) {
                $applicableDiscounts[] = $discount;
            }
        }

        $totalDiscount = array_sum(array_column($applicableDiscounts, 'discountAmount'));
        return [
            'orderId' => $order->id,
            'discounts' => $applicableDiscounts,
            'totalDiscount' => round($totalDiscount, 2),
            'discountedTotal' => round($currentTotal, 2)
        ];
    }
}
