<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;

use Illuminate\Support\Facades\DB;

class OrderService
{
    protected $stockService;
    protected $customerService;

    public function __construct(StockService $stockService, CustomerService $customerService)
    {
        $this->stockService = $stockService;
        $this->customerService = $customerService;
    }

    public function list()
    {
        return Order::with('items.product', 'customer')->get();
    }

    public function createOrder(array $data)
    {
        // Transaction start
        DB::beginTransaction();
        try {

            // Get products from DB and recalculate total
            $products = Product::whereIn('id', array_column($data['items'], 'productId'))->get()->keyBy('id');
            $calculatedTotal = 0;
            $orderItemsData = [];

            foreach ($data['items'] as $item) {
                $product = $products[$item['productId']] ?? null;

                // Recalculate
                $unitPrice = $product->price; // Price from DB
                $itemTotal = $unitPrice * $item['quantity'];
                $calculatedTotal += $itemTotal;

                // Prepare items
                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'total' => $itemTotal,
                ];
            }

            // Check total amount
            if ($calculatedTotal != $data['total']) {
                throw new \Exception("Invalid total amount");
            }

            // Create order
            $order = Order::create([
                'customer_id' => $data['customerId'],
                'total' => $calculatedTotal,
            ]);

            // create order items
            foreach ($orderItemsData as $itemData) {
                $order->items()->create($itemData);
            }

            // Update product stock
            $this->stockService->updateStock($data['items']);

            // Increase customer's revenue
            $this->customerService->increaseRevenue($data['customerId'], $calculatedTotal);

            // Transaction complete
            DB::commit();

            return $order->with('items.product', 'customer')->find($order->id);
        } catch (\Exception $e) {
            // Error rollback
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteOrder(Order $order)
    {
        DB::beginTransaction();
        try {
            // get order items
            $orderItems = $order->items;

            // restock
            $itemsData = [];
            foreach ($orderItems as $item) {
                $itemsData[] = [
                    'productId' => $item->product_id,
                    'quantity' => $item->quantity,
                ];
            }
            $this->stockService->restoreStock($itemsData);

            // decrease revenue
            $this->customerService->decreaseRevenue($order->customer_id, $order->total);

            // delete order items
            $order->items()->delete();

            // delete order
            $order->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

}
