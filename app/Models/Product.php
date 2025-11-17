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

    // Fixed relationships - these were incorrect
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    public function options()
    {
        return $this->belongsToMany(Option::class, 'product_options');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    // Fixed Search Scope - make parameter nullable
    public function scopeSearch(Builder $query, string $search = null)
    {
        if ($search) {
            return $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('descriptions', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    // Fixed Price Range Scope - make parameters nullable
    public function scopePriceRange(Builder $query, float $min = null, float $max = null)
    {
        if ($min) {
            $query->where('price', '>=', $min);
        }
        if ($max) {
            $query->where('price', '<=', $max);
        }
        return $query;
    }

    // Fixed Category Scope - use whereHas for many-to-many relationship
    public function scopeByCategory(Builder $query, int $categoryId = null)
    {
        if ($categoryId) {
            return $query->whereHas('categories', function($q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            });
        }
        return $query;
    }

    // Scope for in stock
    public function scopeInStock(Builder $query)
    {
        return $query->where('stock', '>', 0);
    }
}