<?php

namespace Tests\Unit;

use Database\Factories\OrderFactory;
use Database\Factories\OrderItemFactory;
use Database\Factories\ProductFactory;
use Tests\TestCase;
use App\Services\DiscountService;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DiscountServiceTest extends TestCase
{
    use DatabaseTransactions;

    private DiscountService $discountService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->discountService = new DiscountService();
    }

    /** @test */
    public function it_applies_total_amount_discount_correctly()
    {
        $product = ProductFactory::new()->create(['price' => 50]);
        $quantity = 30;
        $totalAmount = $product->price * $quantity;
        $order = OrderFactory::new()->create(['total' => $totalAmount]);

        OrderItemFactory::new()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'unit_price' => $product->price,
            'total' => $product->price * $quantity,
        ]);

        // Calculate discounts
        $discounts = $this->discountService->calculateDiscounts($order->id);
        $found = false;
        // Check results
        foreach ($discounts['discounts'] as $discount) {
            if ($discount['discountReason'] === '10_PERCENT_OVER_1000') {
                $this->assertEquals(150, $discount['discountAmount']); // %10 discount amount
                $found = true;
            }
        }

        $this->assertTrue($found, "Expected discount '10_PERCENT_OVER_1000' was not found.");
    }

    /** @test */
    public function it_applies_category_bulk_discount_correctly()
    {
        $product = ProductFactory::new()->create(['price' => 50, 'category' => 2]);
        $quantity = 6;
        $totalAmount = $product->price * $quantity;
        $order = OrderFactory::new()->create(['total' => $totalAmount]);

        OrderItemFactory::new()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'unit_price' => $product->price,
            'total' => $product->price * $quantity,
        ]);

        $discounts = $this->discountService->calculateDiscounts($order->id);

        $found = false;
        // Check results
        foreach ($discounts['discounts'] as $discount) {
            if ($discount['discountReason'] === 'BUY_6_GET_1') {
                $this->assertEquals(50.0, $discount['discountAmount']); // %10 discount amount
                $found = true;
            }
        }

        $this->assertTrue($found, "Expected discount 'BUY_6_GET_1' was not found.");
    }

    /** @test */
    public function it_applies_category_cheapest_discount_correctly()
    {
        $product1 = ProductFactory::new()->create(['price' => 100, 'category' => 1]);
        $product2 = ProductFactory::new()->create(['price' => 250, 'category' => 1]);
        $quantity = 6;
        $totalAmount = $product1->price * $quantity;
        $totalAmount += $product2->price * $quantity;
        $order = OrderFactory::new()->create(['total' => $totalAmount]);

        OrderItemFactory::new()->create([
            'order_id' => $order->id,
            'product_id' => $product1->id,
            'quantity' => $quantity,
            'unit_price' => $product1->price,
            'total' => $product1->price * $quantity,
        ]);
        OrderItemFactory::new()->create([
            'order_id' => $order->id,
            'product_id' => $product2->id,
            'quantity' => $quantity,
            'unit_price' => $product2->price,
            'total' => $product2->price * $quantity,
        ]);

        $discounts = $this->discountService->calculateDiscounts($order->id);

        $found = false;
        // Check results
        foreach ($discounts['discounts'] as $discount) {
            if ($discount['discountReason'] === 'CHEAPEST_2_ITEMS_20_PERCENT') {
                $this->assertEquals(20.0, $discount['discountAmount']); // %10 discount amount
                $found = true;
            }
        }

        $this->assertTrue($found, "Expected discount 'CHEAPEST_2_ITEMS_20_PERCENT' was not found.");
    }

    /** @test */
    public function it_returns_no_discounts_if_conditions_are_not_met()
    {
        $product = ProductFactory::new()->create(['price' => 50, 'category' => 1]);
        $quantity = 5;
        $totalAmount = $product->price * $quantity;
        $order = OrderFactory::new()->create(['total' => $totalAmount]);

        OrderItemFactory::new()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'unit_price' => $product->price,
            'total' => $product->price * $quantity,
        ]);

        $discounts = $this->discountService->calculateDiscounts($order->id);

        $this->assertCount(0, $discounts['discounts']);
        $this->assertEquals(250, $discounts['discountedTotal']);
    }
}
