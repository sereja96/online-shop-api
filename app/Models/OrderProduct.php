<?php

namespace App\Models;

use App\Common\CommonScopes;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model implements CommonScopes
{
    protected $table = 'order_product';

    protected $hidden = [
        'created_at',
        'updated_at',
        'order_id',
        'product_id'
    ];

    protected $fillable = [
        'order_id',
        'product_id',
        'count'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class)->withAll();
    }

    public function scopeWithAll($query)
    {
        return $query->with('product');
    }
}
