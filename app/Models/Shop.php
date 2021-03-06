<?php

namespace App\Models;

use App\Common\CommonScopes;
use App\Common\EnableTrait;
use App\Common\ScopesTrait;
use App\Common\SearchTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model implements CommonScopes
{
    use SoftDeletes, EnableTrait, SearchTrait, ScopesTrait;

    protected $table = 'shop';

    protected $hidden = [
        'created_at',
        'updated_at',
        'media_id',
        'user_id',
        'deleted_at',
        'is_enable',
    ];

    protected $fillable = [
        'name',
        'description',
        'media_id',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function image()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class)
            ->withAll()
            ->enabled();
    }

    public function productCount()
    {
        return $this->hasOne(Product::class)
            ->selectRaw('shop_id, count(id) AS count')
            ->enabled()
            ->groupBy('shop_id');
    }

    public function scopeWithAll($query)
    {
        return $query->with(['user', 'image', 'products']);
    }
}
