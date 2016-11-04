<?php

namespace App\Models;

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
        'created_at',
        'updated_at',
        'shop_id',
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

    public function scopeNotDeleted($query)
    {
        return $query->where('is_deleted', false);
    }

    public function scopeWithAll($query)
    {
        return $query->with(['images', 'shop', 'brand', 'category']);
    }

    public function scopeWhereInCategories($query, $categories)
    {
        return $query->whereIn('category_id', $categories);
    }

    public function scopeWhereInBrands($query, $brands)
    {
        return $query->whereIn('brand_id', $brands);
    }
}
