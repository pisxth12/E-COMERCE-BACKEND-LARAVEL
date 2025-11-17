<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;


    protected $fillable = [
        'customer_id', 'amount', 'shipping_address', 'order_address',
        'order_email', 'order_date', 'order_status'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'order_date' => 'datetime',
    ];

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function orderDetails(){
        return $this->hasMany(OrderDetail::class);
    }

    //Search Scope
    public function scopeSearch(Builder $query , string $search = null){
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('order_email', 'like', "%{$search}%")
                  ->orWhere('shipping_address', 'like', "%{$search}%")
                  ->orWhere('order_address', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($customerQuery) use ($search) {
                      $customerQuery->where('full_name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        return $query;
    }


    //Scope for order status
    public function scopeByStatus(Builder $query , string $status = null){
        if($status){
            return $query->where('order_status' , 'like' , "%$status%");
        }
        return $query;
    }

    //Scope for date range
    public function scopeDateRange(Builder $query , string $startDate = null , string $endDate = null){
        if($startDate){
            $query->where('order_date' , '>=', $startDate);
        }
        if($endDate){
            $query->where('order_date' , '<=', $endDate);
        }
        return $query;
    }

    //Scope for customer 
    public function scopeByCustomer(Builder $query , int $customerId = null){
        if($customerId){
            return $query->where('customer_id' , $customerId);
        }
        return $query;
    }

    //Scope for recent orders
    public function scopeRecent(Builder $query , int $days = null){
        return $query->where('order_date', '>=', now()->subDays($days));
    }


}
