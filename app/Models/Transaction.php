<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'order_id',
        'mode',
        'status',
        'amount',
        'reference_id',
        'card_number',
        'card_expiry',
        'card_cvv',
        'card_holder_name'
    ];
    
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Status constants for better code maintainability
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_DECLINED = 'declined';
    const STATUS_REFUNDED = 'refunded';

    // Payment mode constants
    const MODE_COD = 'cod';
    const MODE_CARD = 'card';
    const MODE_PAYPAL = 'paypal';

    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_DECLINED => 'Declined',
            self::STATUS_REFUNDED => 'Refunded',
        ];
    }

    public static function getModes()
    {
        return [
            self::MODE_COD => 'Cash on Delivery',
            self::MODE_CARD => 'Credit/Debit Card',
            self::MODE_PAYPAL => 'PayPal',
        ];
    }
}
