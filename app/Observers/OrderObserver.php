<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;

class OrderObserver
{
    /**
     * Handle events after an order is updated.
     */
    public function updated(Order $order)
    {
        // If order is delivered
        if ($order->isDirty('status') && $order->status === 'delivered') {
            $order->delivered_date = Carbon::now();
            $order->saveQuietly(); // prevent infinite loop

            // Reduce product quantities
            $orderItems = OrderItem::where('order_id', $order->id)->get();
            foreach ($orderItems as $item) {
                $product = Product::find($item->product_id);
                if ($product && $product->quantity >= $item->quantity) {
                    $product->decrement('quantity', $item->quantity);
                }
            }

            // Update transaction
            $transaction = Transaction::where('order_id', $order->id)->first();
            if ($transaction) {
                $transaction->status = 'approved';
                $transaction->save();
            }
        }

        // If order is cancelled
        if ($order->isDirty('status') && $order->status === 'cancelled') {
            $order->cancelled_date = Carbon::now();
            $order->saveQuietly();
        }
    }
}
