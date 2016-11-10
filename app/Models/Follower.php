<?php

namespace App\Models;

use App\Common\ScopesTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Follower extends Model
{
    use SoftDeletes, ScopesTrait;

    protected $table = 'follower';

    protected $fillable = [
        'user_id', 'follower_user_id'
    ];

    protected $hidden = [
        'deleted_at'
    ];

    // Scopes
    public function scopeWhereIFollow($query, $userId)
    {
        return $query->where('user_id', $userId)
            ->where('follower_user_id', User::myId());
    }
}
