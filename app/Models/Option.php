<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    protected $fillable = [
        'option_name'
    ];

    public function products(){
        return $this->belongsToMany(Product::class, 'product_options');
    }

}
