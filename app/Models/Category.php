<?php

namespace App\Models;

use App\Common\CommonScopes;
use App\Common\ScopesTrait;
use Illuminate\Database\Eloquent\Model;

class Category extends Model implements CommonScopes
{
    use ScopesTrait;

    protected $table = 'category';

    protected $fillable = [
        'name'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function products()
    {
        return $this->hasMany(Product::class)
            ->withAll()
            ->enabled();
    }

    public function productCount()
    {
        return $this->hasOne(Product::class)
            ->selectRaw('category_id, count(id) AS count')
            ->enabled()
            ->groupBy('category_id');
    }

    public function image()
    {
        return $this->hasOne(Product::class)
            ->selectRaw("null");
    }

    public function scopeWithAll($query)
    {
        return $query->with(['image', 'products']);
    }
}
