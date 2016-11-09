<?php

namespace App\Models;

use App\CommonModel;

class Role extends CommonModel
{
    const ADMIN = 'admin';
    const USER = 'user';
    const SELLER = 'seller';

    protected $table = 'role';

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'name'
    ];

    public function users()
    {
        return $this->hasMany('App\User');
    }
}
