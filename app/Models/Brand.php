<?php

namespace App\Models;

use App\CommonModel;

class Brand extends CommonModel
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
        return $this->hasMany(Product::class);
    }

    public function image()
    {
        return $this->belongsTo(Media::class);
    }
}
