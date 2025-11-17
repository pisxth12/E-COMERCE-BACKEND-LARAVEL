<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    protected $fillable = [
        'option_name', 'description'
    ];

    public function products(){
        return $this->belongsToMany(Product::class, 'product_options');
    }

    //Search Scope
    public function scopeSearch(Builder $query , string $search = null){
        if($search){
            return $query->where('option_name' , 'like' , "%$search%")
            ->orWhere('description' , 'like' , "%$search%");
        }
    }

}
