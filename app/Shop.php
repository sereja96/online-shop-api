<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $table = 'shop';

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'name',
        'description',
        'media_id',
        'user_id',
    ];

    public function user()
    {
        $this->belongsTo('App\User');
    }

    public function image()
    {
        $this->belongsTo('App\Media');
    }

    public function products()
    {
        $this->hasMany('App\Product');
    }
}
