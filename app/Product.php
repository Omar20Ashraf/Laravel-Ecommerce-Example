<?php

namespace App;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use Searchable;

    protected $fillable = ['quantity'];
    
    public function presentPrice()
    {
        return '$'.number_format($this->price / 100, 2);
    }

    public function scopeMightAlsoLike($query)
    {
        return $query->inRandomOrder()->take(4);
    }

    public function categories()
    {
    	return $this->belongsToMany(Category::class);
    }

    // public function orders()
    // {
    //     return $this->belongsToMany(Order::class);
    // }
}
