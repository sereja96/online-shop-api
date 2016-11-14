<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaProduct extends Model
{
    use SoftDeletes;

    protected $hidden = [
        'deleted_at'
    ];

    protected $table = 'media_product';
}
