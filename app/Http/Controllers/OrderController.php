<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\StockService;
// TODO: use App\Jobs\SendOrderConfirmationNotification;

class OrderController extends BaseController
{
    protected $stockService;

    public function __construct(StockService $stockService, OrderService $orderService)
    {
        $this->stockService = $stockService;
        $this->orderService = $orderService;
    }

    public function index()
    {
        return $this->orderService->list();
    }

    public function store(StoreOrderRequest $request)
    {
        $data = $request->validated();
        try {
            $order = $this->orderService->createOrder($data);

            // TODO: order confirmation notification
            //$customer = $order->customer;
            //$method = $customer->preferences['notification_method'] ?? 'email';
            //dispatch(new \App\Jobs\SendOrderConfirmationNotification($order, $method));

            return response()->json($order, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function destroy(Order $order)
    {
        try {
            $this->orderService->deleteOrder($order);
            return response()->json(['message' => 'Order deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Order deletion failed'], 500);
        }
    }

}
