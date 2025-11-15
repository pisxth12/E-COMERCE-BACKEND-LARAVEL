<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'sku',
        'category',
        'status',
        'specifications'
    ];

    protected $casts = [
        'price' => 'float',
        'specifications' => 'array',
        'stock' => 'integer'
    ];

    //Relationship with product images
    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }
    // Get primary image
    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

      // Get first image URL (for backward compatibility)
     public function getFirstImageUrlAttribute()
    {
        $image = $this->images->first();
        return $image ? asset('storage/' . $image->image_path) : null;
    }
    // Get primary image URL
    public function getPrimaryImageUrlAttribute()
    {
        $image = $this->primaryImage ?: $this->images->first();
        return $image ? asset('storage/' . $image->image_path) : null;
    }




    // Search Scope
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
    }

    //filter by status
    public function scopeStatus($query, $status){
        return $query->where('status', $status);
    }

    // Filter by category
    public function scopeCategory($query, $category){
        return $query->where('category', $category);
    }

    //check if product is in stock
    public function isInStock(){
        return $this->stock > 0;
    }
    

    //format price with currency
     public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }

}

