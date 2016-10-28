<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    protected $table = 'follower';

    public static $_STATUS_REQUEST = 'request';
    public static $_STATUS_SUBMIT = 'submit';

    public static function getStatuses()
    {
        return [
            self::$_STATUS_REQUEST,
            self::$_STATUS_SUBMIT,
        ];
    }

    protected $fillable = [
        'user_id', 'follower_user_id', 'status'
    ];

    protected $hidden = [
        'user_id', 'follower_user_id'
    ];
}
