<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'product_id', 'price', 'sku', 'quantity'
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function order(){
        return $this->belongsTo(Order::class);
    }

    protected function product(){
         return $this->belongsTo(Product::class);
    }

}
