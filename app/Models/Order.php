<?php

namespace App\Models;

use App\Common\CommonScopes;
use App\Common\ScopesTrait;
use Illuminate\Database\Eloquent\Model;

class Order extends Model implements CommonScopes
{
    use ScopesTrait;

    protected $table = 'order';

    protected $hidden = [
        'user_id',
        'discount_id',
        'pivot',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->with('image');
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    public function products()
    {
        return $this->hasMany(OrderProduct::class)->withAll();
    }

    public function scopeWithAll($query)
    {
        return $query->with(['user', 'discount', 'products']);
    }
}
