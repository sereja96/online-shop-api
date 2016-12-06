<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $table = 'discount';

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
