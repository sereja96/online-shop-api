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
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
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
