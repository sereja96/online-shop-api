<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $table = 'brand';

    protected $fillable = [
        'name'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'is_deleted',
        'media_id',
    ];

    public function products()
    {
        return $this->hasMany('App\Models\Product');
    }

    public function image()
    {
        return $this->belongsTo('App\Models\Media');
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where('name', 'LIKE', $search.'%');
        }
        return $query;
    }

    public function scopeNotDeleted($query)
    {
        return $query->where('is_deleted', false);
    }
}
