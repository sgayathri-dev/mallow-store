<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Jobs\SendOrderConfirmationJob;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder(array $data): Order
    {
        $order = DB::transaction(function () use ($data) {

            $customer = Customer::firstOrCreate(
                ['email' => $data['customer_email']],
                ['name' => $data['customer_name'] ?? $data['customer_email']]
            );

            $subtotal = 0;
            $tax = 0;

            $order = Order::create([
                'customer_id' => $customer->id,
                'subtotal' => 0,
                'tax' => 0,
                'grand_total' => 0,
            ]);

            foreach ($data['items'] as $item) {

                $product = Product::query()
                    ->whereKey($item['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $quantity = $item['quantity'];

                if ($product->stock_on_hand < $quantity) {
                    throw new InsufficientStockException($product->name);
                }

                $unitPrice = $product->price;
                $taxPercentage = $product->tax_percentage;

                $lineSubtotal = $quantity * $unitPrice;
                $lineTax = $lineSubtotal * ($taxPercentage / 100);
                $lineTotal = $lineSubtotal + $lineTax;

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'tax_percentage' => $taxPercentage,
                    'line_subtotal' => $lineSubtotal,
                    'line_tax' => $lineTax,
                    'line_total' => $lineTotal,
                ]);

                $product->decrement('stock_on_hand', $quantity);

                $subtotal += $lineSubtotal;
                $tax += $lineTax;
            }

            $order->update([
                'subtotal' => $subtotal,
                'tax' => $tax,
                'grand_total' => $subtotal + $tax,
            ]);

            return $order->load('customer', 'items.product');
        });

        SendOrderConfirmationJob::dispatch($order);

        return $order;
    }
}

