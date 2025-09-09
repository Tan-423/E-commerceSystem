<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        return view('user.index');
    }

    public function orders()
    {
        $orders = Order::where('user_id', Auth::id())
            ->orderBy('created_at', 'ASC')
            ->paginate(10);

        return view('user.orders', compact('orders'));
    }

    public function order_details($order_id)
    {
        $order = Order::where('user_id', Auth::id())->find($order_id);
        if (!$order) {
            return redirect()->route('login');
        }

        $orderItems = OrderItem::where('order_id', $order_id)->orderBy('id')->paginate(12);
        $transaction = Transaction::where('order_id', $order_id)->first();

        return view('user.order-details', compact('order', 'orderItems', 'transaction'));
    }

    public function order_cancel(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->status = 'cancelled';
        $order->save(); // Observer handles cancelled_date

        return back()->with('status', 'Order has been cancelled successfully!');
    }
}
