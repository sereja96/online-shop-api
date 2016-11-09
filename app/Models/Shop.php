<?php

namespace App\Models;

use App\CommonModel;

class Shop extends CommonModel
{
    protected $table = 'shop';

    protected $hidden = [
        'created_at',
        'updated_at',
        'media_id',
        'user_id',
        'is_deleted',
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
        return $this->belongsTo('App\User');
    }

    public function image()
    {
        return $this->belongsTo('App\Media');
    }

    public function products()
    {
        return $this->hasMany('App\Product')->with(['images']);
    }
}
