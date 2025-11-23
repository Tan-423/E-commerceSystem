<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class AdminController extends Controller
{
    public function index()
    {
        $orders = Order::orderBy('created_at', 'ASC')->paginate(12);

        $dashboardDatas = DB::table('orders')
            ->selectRaw('
                SUM(total) as TotalAmount,
                SUM(CASE WHEN status = "ordered" THEN total ELSE 0 END) as TotalOrderedAmount,
                SUM(CASE WHEN status = "delivered" THEN total ELSE 0 END) as TotalDeliveredAmount,
                SUM(CASE WHEN status = "cancelled" THEN total ELSE 0 END) as TotalCancelledAmount,
                COUNT(*) as Total,
                SUM(CASE WHEN status = "ordered" THEN 1 ELSE 0 END) as TotalOrdered,
                SUM(CASE WHEN status = "delivered" THEN 1 ELSE 0 END) as TotalDelivered,
                SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as TotalCancelled
            ')
            ->get();

        $monthDates = DB::select("
            SELECT M.id AS MonthNo, M.name AS MonthName,
                IFNULL(D.TotalAmount, 0) AS TotalAmount,
                IFNULL(D.TotalOrderedAmount, 0) AS TotalOrderedAmount,
                IFNULL(D.TotalDeliveredAmount, 0) AS TotalDeliveredAmount,
                IFNULL(D.TotalCancelledAmount, 0) AS TotalCancelledAmount
            FROM month_names M
            LEFT JOIN(
                SELECT MONTH(created_at) AS MonthName,
                    SUM(total) as TotalAmount,
                    SUM(IF(status = 'ordered', total, 0)) as TotalOrderedAmount,
                    SUM(IF(status = 'delivered', total, 0)) as TotalDeliveredAmount,
                    SUM(IF(status = 'cancelled', total, 0)) as TotalCancelledAmount
                FROM orders 
                WHERE YEAR(created_at) = YEAR(NOW()) 
                GROUP BY MONTH(created_at)
                ORDER BY MONTH(created_at)
            ) D ON D.MonthName = M.id
        ");

        $AmountM = implode(',', collect($monthDates)->pluck('TotalAmount')->toArray());
        $OrderAmountM = implode(',', collect($monthDates)->pluck('TotalOrderedAmount')->toArray());
        $DeliveredAmountM = implode(',', collect($monthDates)->pluck('TotalDeliveredAmount')->toArray());
        $CancelledAmountM = implode(',', collect($monthDates)->pluck('TotalCancelledAmount')->toArray());

        $TotalAmount = collect($monthDates)->sum('TotalAmount');
        $TotalOrderedAmount = collect($monthDates)->sum('TotalOrderedAmount');
        $TotalDeliveredAmount = collect($monthDates)->sum('TotalDeliveredAmount');
        $TotalCancelledAmount = collect($monthDates)->sum('TotalCancelledAmount');

        return view('admin.index', compact(
            'orders',
            'dashboardDatas',
            'AmountM',
            'OrderAmountM',
            'DeliveredAmountM',
            'CancelledAmountM',
            'TotalAmount',
            'TotalOrderedAmount',
            'TotalDeliveredAmount',
            'TotalCancelledAmount'
        ));
    }

    public function orders()
    {
        $orders = Order::orderBy('created_at', 'ASC')->paginate(12);
        
        // Calculate total order items by each user
        $userOrderStats = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->select('orders.name', 'orders.phone')
            ->selectRaw('
                COUNT(DISTINCT orders.id) as total_orders,
                SUM(order_items.quantity) as total_items,
                SUM(orders.total) as total_amount
            ')
            ->groupBy('orders.name', 'orders.phone')
            ->orderBy('total_items', 'DESC')
            ->get();
        
        return view('admin.orders', compact('orders', 'userOrderStats'));
    }

    public function order_details($order_id)
    {
        $order = Order::findOrFail($order_id);
        $orderitems = OrderItem::where('order_id', $order_id)->orderBy('id')->paginate(12);
        $transaction = Transaction::where('order_id', $order_id)->first();

        return view('admin.order-details', compact('order', 'orderitems', 'transaction'));
    }

    public function update_order_status(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        
        // Check if trying to set status to 'delivered' and validate product quantities
        if ($request->order_status === 'delivered') {
            $orderItems = OrderItem::where('order_id', $order->id)->with('product')->get();
            $outOfStockProducts = [];
            
            foreach ($orderItems as $item) {
                if ($item->product && $item->product->quantity == 0) {
                    $outOfStockProducts[] = $item->product->name;
                }
            }
            
            if (!empty($outOfStockProducts)) {
                $productList = implode(', ', $outOfStockProducts);
                return back()->with('error', "Cannot mark order as delivered. The following products have zero quantity: {$productList}. Please add stock before marking as delivered.");
            }
        }
        
        $order->status = $request->order_status;
        $order->save(); // Observer handles business logic

        return back()->with('status', "Status changed successfully!");
    }

    public function users()
    {
        $users = User::orderBy('created_at', 'ASC')->paginate(10);
        return view('admin.users', compact('users'));
    }

    public function payments(Request $request)
    {
        $query = Transaction::with(['order' => function($query) {
            $query->with(['orderItems.product', 'user']);
        }]);

        // Filter by status if provided
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by payment mode if provided
        if ($request->has('mode') && $request->mode != '') {
            $query->where('mode', $request->mode);
        }

        // Search by order number or user details
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->whereHas('order', function($q) use ($searchTerm) {
                $q->where('global_order_number', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('name', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('email', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $transactions = $query->orderBy('created_at', 'ASC')->paginate(12);

        // Get unique statuses and modes for filter dropdowns
        $statuses = Transaction::distinct()->pluck('status');
        $modes = Transaction::distinct()->pluck('mode');

        return view('admin.payments', compact('transactions', 'statuses', 'modes'));
    }

    public function lock_user($user_id)
    {
        try {
            $user = User::findOrFail($user_id);
            
            if ($user->locked) {
                return back()->with('info', 'User is already locked.');
            }
            
            DB::transaction(function () use ($user) {
                $user->locked = true;
                $user->save();
            });
            
            return back()->with('status', 'User has been locked successfully!');
            
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'User not found!');
        } catch (Exception $e) {
            Log::error('Failed to lock user: ' . $e->getMessage(), [
                'user_id' => $user_id,
                'admin_id' => Auth::id()
            ]);
            
            return back()->with('error', 'Failed to lock user. Please try again.');
        }
    }

    public function unlock_user($user_id)
    {
        try {
            // Use findOrFail for better error handling
            $user = User::findOrFail($user_id);
            
            // Check if user is actually locked
            if (!$user->locked) {
                return back()->with('info', 'User is already unlocked.');
            }
            
            // Use database transaction to ensure consistency
            DB::transaction(function () use ($user) {
                $user->locked = false;
                $user->failed_attempts = 0;
                $user->last_failed_attempt = null;
                $user->save();
            });
            
            return back()->with('status', 'User has been unlocked successfully!');
            
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'User not found!');
        } catch (Exception $e) {
            // Log the error for debugging
            Log::error('Failed to unlock user: ' . $e->getMessage(), [
                'user_id' => $user_id,
                'admin_id' => Auth::id()
            ]);
            
            return back()->with('error', 'Failed to unlock user. Please try again.');
        }
    }
}

