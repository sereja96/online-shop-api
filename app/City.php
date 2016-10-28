<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'city';

    protected $hidden = [
        'country_id',
        'created_at',
        'updated_at'
    ];

    public function users()
    {
        return $this->hasMany('App\User');
    }

    public function country()
    {
        return $this->belongsTo('App\Country');
    }

}
