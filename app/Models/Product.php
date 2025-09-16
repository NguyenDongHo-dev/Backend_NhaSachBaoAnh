<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Product extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'description',
        'short_description',
        'discount',
        'price',
        'slug',
        'category_id',
        'status',
        'sold',
        'stock'
    ];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name')) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function image()
    {
        return $this->hasMany(Product_image::class);
    }
}
