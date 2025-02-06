<?php

namespace App\Services\Discounts;

use App\Models\Order;

interface DiscountInterface
{
    public function apply(Order $order, float &$currentTotal): array;
}
