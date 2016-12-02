<?php

namespace App\Models;

use App\Basket;
use App\Common\CommonScopes;
use App\Common\EnableTrait;
use App\Common\ScopesTrait;
use App\Common\SearchTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable implements CommonScopes
{
    use SoftDeletes, EnableTrait, SearchTrait, ScopesTrait;

    protected $table = 'user';

    protected $searchField = 'login';

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

    public static function isMyId($id)
    {
        return $id == User::myId();
    }

    public static function myId()
    {
        return Auth::check()
            ? Auth::user()->id
            : null;
    }

    public function follow($userId)
    {
        $errorMessage = null;

        if (User::isMyId($userId)) {
            $errorMessage = trans('messages.access_deny');
        } elseif (!$user = User::find($userId)) {
            $errorMessage = trans('messages.not_found', ['item' => trans('model.user')]);
        }

        if ($errorMessage) {
            return $errorMessage;
        }

        Follower::create([
            'user_id' => $user->id,
            'follower_user_id' => User::myId()
        ]);

        return false;
    }

    // Relations
    public function role()
    {
        return $this->belongsTo(Role::class)
            ->select('id', 'name');
    }

    public function image()
    {
        return $this->belongsTo(Media::class, 'media_id')
            ->select('id', 'link');
    }

    public function countFollow()
    {
        return $this->hasOne(Follower::class, $this->getUserIdColumn())
            ->selectRaw($this->getUserIdColumn() . ', count(*) AS count')
            ->groupBy($this->getUserIdColumn());
    }

    public function countFollowed()
    {
        return $this->hasOne(Follower::class, 'follower_user_id')
            ->selectRaw('follower_user_id, count(*) AS count')
            ->groupBy('follower_user_id');
    }

    public function followersIds()
    {
        return $this->hasMany(Follower::class, $this->getUserIdColumn())
            ->lists('follower_user_id');
    }

    public function followedIds()
    {
        return $this->hasMany(Follower::class, 'follower_user_id')
            ->lists($this->getUserIdColumn());
    }

    // Scopes
    public function scopeMe($query)
    {
        return $query->where($this->getIdColumn(), self::myId());
    }

    public function scopeWithAll($query)
    {
        return $query->with(['role', 'image', 'countFollow', 'countFollowed']);
    }
}
