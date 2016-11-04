<?php

namespace App\Models;

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

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
