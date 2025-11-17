<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

     protected $fillable = [
        'sku', 'name', 'price', 'size', 'descriptions', 
        'thumbnail', 'image', 'category', 'create_date', 'stock'
    ];

     protected $casts = [
        'price' => 'decimal:2',
        'create_date' => 'datetime',
    ];

    public function categories(){
        return $this->belongsTo(Category::class, 'product_categories');
    }

    public function options(){
        return $this->belongsTo(Option::class, 'product_options');
    }

    public function orderDetails(){
        return $this->hasMany(OrderDetail::class);
    }


    //Search Scope
    public function scopeSearch(Builder $query , string $search){
       if($search){
            return $query->where(function($q) use($search){
                $q->where('name' , 'like' , "%$search%")
                ->orWhere('descriptions' , 'like' , "%$search%")
                ->orWhere('sku' , 'like' , "%$search%");
            });
       }
       return $query;
    }

    //Scope for price range
    public function scopePriceRange(Builder $query , float $min , float $max = null){
        if($min){
            $query->where('price' , '>=', $min);
        }
        if($max){
            $query->where('price' , '<=', $max);
        }
        return $query;
    }

    //Scope search category
    public function scopeSearchCategory(Builder $query , int $categoryId = null){
    
        if($categoryId){
            return $query->where(function($q) use($categoryId){
                $q->where('categories.id', $categoryId);
            });
        }
        return $query;
    }

    //Scope for in stock
    public function scopeInStock(Builder $query ){
        return $query->where('stock' , '>' , 0);
    }






        

}
