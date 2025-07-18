<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;



class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_method',
        'order_number',
        'total_price',
        'status',
        'user_noteid',
        'paid_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                do {
                    $random = 'ORD' . strtoupper(Str::random(6));
                } while (self::where('order_number', $random)->exists());
                $order->order_number = $random;
            }
        });
    }
}
