<?php

namespace App\Models;

use App\Common\EnableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes, EnableTrait;

    protected $table = 'comment';
}
