<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'thumbnail'
    ];

    public function products(){
        return $this->belongsToMany(Product::class, 'product_categories');
    }

    //Search Scope
    public function scopeSearch(Builder $query , string $search = null){
        if($search){
            return $query->where(function($q) use($search){
                $q->where('name' , 'like' , "%$search%")
                  ->orWhere('description' , 'like' , "%$search%");
            });
        }
        return $query;
    }

}

