<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
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

    public function scopeNotDeleted($query)
    {
        return $query->where('is_deleted', false);
    }

    public function scopeEnabled($query)
    {
        return $query->where('is_enable', true);
    }

    public function scopeWhereMy($query)
    {
        return $query->where('user_id', User::myId());
    }
}
