<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable =[
        'email',
        'password', 
        'full_name', 
        'billing_address',
        'default_shipping_address', 
        'country', 
        'phone'
    ];

    protected $hidden = [
        'password'
    ];

    //Relationships
    public function orders(){
        return $this->hasMany(Order::class);
    }

    //Search Scope
    public function scopeSearch(Builder $query , string $search =null){
        if($search){
            return $query->where(function($q) use($search){
                $q->where('full_name' , 'like' , "%$search%")
                  ->orWhere('email' , 'like' , "%$search%")
                  ->orWhere('phone' , 'like' , "%$search%")
                  ->orWhere('country' , 'like' , "%$search%");
            });
        }
        return $query;
    }

    //Scope by country
    public function scopeByCountry(Builder $query , string $country = null){
        if($country){
            return $query->where('country' , 'like' , "%$country%");
        }
        return $query;
    }


}
