<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id', 'subtotal', 'discount', 'tax', 'total', 'name', 'phone', 
        'locality', 'address', 'city', 'state', 'country', 'landmark', 'zip', 
        'type', 'status', 'is_shipping_different', 'delivered_date', 'cancelled_date',
        'order_number', 'global_order_number'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                // Get the count of orders for this specific user and add 1
                $userOrderCount = static::where('user_id', $order->user_id)->count();
                $order->order_number = $userOrderCount + 1;
            }
            if (empty($order->global_order_number)) {
                // Get the total count of all orders and add 1 for global order numbering (admin use)
                $totalOrderCount = static::count();
                $order->global_order_number = $totalOrderCount + 1;
            }
        });
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    
    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
}
