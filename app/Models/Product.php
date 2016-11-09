<?php

namespace App\Models;

use App\CommonModel;

class Product extends CommonModel
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
        return $this->belongsTo(Shop::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function images()
    {
        return $this->hasMany(MediaProduct::class);
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
