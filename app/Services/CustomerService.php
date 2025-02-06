<?php

namespace App\Services;

use App\Models\Customer;

class CustomerService
{
    public function increaseRevenue(int $customerId, float $amount): void
    {
        $customer = Customer::findOrFail($customerId);
        if ($customer) {
            $customer->increment('revenue', $amount);
        }
    }

    public function decreaseRevenue(int $customerId, float $amount)
    {
        $customer = Customer::findOrFail($customerId);
        if ($customer) {
            $customer->decrement('revenue', $amount);
        }
    }

}
