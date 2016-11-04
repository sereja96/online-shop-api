<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'media_id',
        'login',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'role_id',
        'media_id',
        'created_at',
        'updated_at',
        'is_deleted',
        'is_enable',
        'pivot'
    ];

    public static $_VALID_DATA = [
        'MIN' => [
            'LOGIN' => 3,
            'PASSWORD' => 4,
            'FIRST_NAME' => 3,
        ],
        'MAX' => [
            'LOGIN' => 255,
            'PASSWORD' => 255,
            'FIRST_NAME' => 255,
        ],
        'LABEL' => [
            'LOGIN' => 'Логин',
            'PASSWORD' => 'Пароль',
            'FIRST_NAME' => 'Имя пользователя',
        ]
    ];

    protected $table = 'user';

    public static function isMyId($id)
    {
        return $id == User::myId();
    }

    public static function myId()
    {
        return 1; // Auth::user()->id;
    }

    public function scopeMy($query)
    {
        return $query->where('user_id', self::myId());
    }

    public function scopeMe($query)
    {
        return $query->where('id', self::myId());
    }

    public function role()
    {
        return $this->belongsTo('App\Role')
            ->select('id', 'name');
    }

    public function image()
    {
        return $this->belongsTo('App\Media', 'media_id')
            ->select('id', 'link');
    }

    public function countFollow()
    {
        return $this->hasOne('App\Follower', 'user_id')
            ->selectRaw('user_id, count(*) AS count')
            ->groupBy('user_id');
    }

    public function countFollowed()
    {
        return $this->hasOne('App\Follower', 'follower_user_id')
            ->selectRaw('follower_user_id, count(*) AS count')
            ->groupBy('follower_user_id');
    }

}
