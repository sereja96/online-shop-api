<?php

namespace App\Models;

use App\CommonModel;

class Follower extends CommonModel
{
    protected $table = 'follower';

    protected $fillable = [
        'user_id', 'follower_user_id', 'status'
    ];

    protected $hidden = [
        'user_id', 'follower_user_id'
    ];

    public static function getFollowerIds($userId)
    {
        return self::where('user_id', $userId)
            ->notDeleted()
            ->lists('follower_user_id AS id');
    }

    public static function getFollowedIds($userId)
    {
        return self::where('follower_user_id', $userId)
            ->notDeleted()
            ->lists('user_id AS id');
    }

    // Scopes
    public function scopeWhereIFollow($query, $userId)
    {
        return $query->where('user_id', $userId)
            ->where('follower_user_id', User::myId());
    }
}
