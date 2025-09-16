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
        'order_number',
        'shipping_address',
        'recipient_phone',
        'order_recipient_name',
        'delivery_method',
        'total_price',
        'price_shipping',
        'total_all',
        'status',
        'notes',
        'paid',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
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

    public function order_items()
    {
        return $this->hasMany(Order_item::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
