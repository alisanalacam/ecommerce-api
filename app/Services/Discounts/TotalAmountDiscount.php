<?php

namespace App\Services\Discounts;

use App\Models\Order;

class TotalAmountDiscount implements DiscountInterface
{
    private float $threshold;
    private float $discountRate;

    public function __construct(float $threshold = 1000, float $discountRate = 0.10)
    {
        $this->threshold = $threshold;
        $this->discountRate = $discountRate;
    }

    public function apply(Order $order, float &$currentTotal): array
    {
        if ($order->total >= $this->threshold) {
            $discountAmount = round($currentTotal * $this->discountRate, 2);
            $currentTotal = round($currentTotal - $discountAmount, 2);

            return [
                'discountReason' => $this->discountRate * 100 . '_PERCENT_OVER_' . $this->threshold,
                'discountAmount' => $discountAmount,
                'subtotal' => $currentTotal
            ];
        }

        return [];
    }
}
