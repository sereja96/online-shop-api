<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = 'media';

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'link'
    ];
}
