<?php

namespace App\Http\Controllers;

use App\Services\DiscountService;
use Illuminate\Http\Request;

class DiscountController extends BaseController
{
    private $discountService;

    public function __construct(DiscountService $discountService)
    {
        $this->discountService = $discountService;
    }
    public function applyDiscounts(Request $request)
    {
        return $this->discountService->calculateDiscounts($request->order_id);
    }
}
