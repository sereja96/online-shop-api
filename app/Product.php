<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'product';

    protected $fillable = [
        'name',
        'description',
        'user_id',
        'category_id',
        'brand_id',
    ];

    protected $hidden = [
        'user_id',
        'category_id',
        'brand_id',
    ];

    public function shop()
    {
        return $this->belongsTo('App\Shop');
    }

    public function brand()
    {
        return $this->belongsTo('App\Brand');
    }

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    public function images()
    {
        return $this->hasMany('App\MediaProduct');
    }
}
