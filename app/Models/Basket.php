<?php

namespace App\Models;

use App\Common\CommonScopes;
use App\Common\ScopesTrait;
use Illuminate\Database\Eloquent\Model;

class Basket extends Model implements CommonScopes
{
    use ScopesTrait;

    protected $table = 'basket';

    protected $fillable = [
        'user_id',
        'product_id',
        'count'
    ];

    protected $hidden = [
        'user_id',
        'product_id'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeWithAll($query)
    {
        return $query->with(['product']);
    }
}
