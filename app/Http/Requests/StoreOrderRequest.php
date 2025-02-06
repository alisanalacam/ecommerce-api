<?php
namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customerId' => 'required|exists:customers,id',
            'total' => 'required|numeric',
            'items' => 'required|array|min:1',
            'items.*.productId' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unitPrice' => 'required|numeric',
            'items.*.total' => 'required|numeric',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $items = request('items');

            if ($items) {
                foreach ($items as $index => $item) {
                    $product = Product::find($item['productId']);

                    if (!$product || $product->stock < $item['quantity']) {
                        $validator->errors()->add("items.{$index}.quantity", 'Insufficient stock.');
                    }
                }
            }
        });
    }
}
