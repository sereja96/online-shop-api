<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
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
        return $this->hasMany(User::class);
    }
}
