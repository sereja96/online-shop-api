<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'country';

    protected $hidden = [
        'created_at',
        'updated_at',
        'pivot'
    ];

    public function users()
    {
        return $this->belongsToMany('App\User', 'country_user');
    }
}
