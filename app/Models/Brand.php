<?php

namespace App\Models;

use App\Common\CommonScopes;
use App\Common\EnableTrait;
use App\Common\ScopesTrait;
use App\Common\SearchTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model implements CommonScopes
{
    use SoftDeletes, EnableTrait, SearchTrait, ScopesTrait;

    protected $table = 'brand';

    protected $fillable = [
        'name'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'media_id',
        'is_enable',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function productCount()
    {
        return $this->hasOne(Product::class)
            ->selectRaw('brand_id, count(id) AS count')
            ->enabled()
            ->groupBy('brand_id');
    }

    public function image()
    {
        return $this->belongsTo(Media::class);
    }

    public function scopeWithAll($query)
    {
        return $query->with(['image', 'products']);
    }
}
