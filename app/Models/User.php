<?php

namespace App\Models;

use App\bool;
use App\CommonScopes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class User extends Authenticatable implements CommonScopes
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
        return Auth::check()
            ? Auth::user()->id
            : null;
    }

    public static function auth($credentials)
    {
        try {
            $token = JWTAuth::attempt($credentials);
        } catch (JWTException $e) {
            return false;
        }

        return $token;
    }

    public static function searchUsers($ids = null)
    {
        $users = self::withAll()
            ->whereInIds($ids)
            ->notDeleted()
            ->enabled()
            ->get();

        return $users;
    }

    public static function getUser($id)
    {
        $user = self::withAll()
            ->whereInIds([$id])
            ->notDeleted()
            ->enabled()
            ->first();

        return $user;
    }

    public static function checkForFollow($userId)
    {
        $errorMessage = null;

        if (User::isMyId($userId)) {
            $errorMessage = trans('messages.your_self');
        }

        if (!self::isExists($userId)) {
            $errorMessage = trans('messages.not_found', ['item' => trans('model.user')]);
        }

        return $errorMessage;
    }

    public static function isExists($userId)
    {
        return User::find($userId);
    }

    public static function isExistsLogin($login)
    {
        return User::where('login', $login)
            ->first();
    }

    public static function follow($userId)
    {
        return self::create([
            'user_id' => $userId,
            'follower_user_id' => User::myId()
        ]);
    }

    public static function checkValidData($dataArray, &$errors) {
        if (!is_array($dataArray)) {
            return true;
        }

        if (!is_array($errors)) {
            $errors = [];
        }

        foreach ($dataArray as $key => $dataField)
        {
            if (!empty(User::$_VALID_DATA['MIN'][$key])
                && mb_strlen($dataField) < User::$_VALID_DATA['MIN'][$key]) {
                array_push(
                    $errors,
                    User::$_VALID_DATA['LABEL'][$key] . " не может быть короче " .User::$_VALID_DATA['MIN'][$key]. " символов"
                );
            }

            if (!empty(User::$_VALID_DATA['MAX'][$key])
                && mb_strlen($dataField) > User::$_VALID_DATA['MAX'][$key]) {
                array_push(
                    $errors,
                    User::$_VALID_DATA['LABEL'][$key] . " не может быть длинее " .User::$_VALID_DATA['MAX'][$key]. " символов"
                );
            }
        }

        return empty($errors);
    }

    public function updateDeleted($isDeleted)
    {
        $this->is_deleted = $isDeleted;
        return $this->saveOrFail();
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
        return $this->hasOne(Follower::class, 'user_id')
            ->selectRaw('user_id, count(*) AS count')
            ->notDeleted()
            ->groupBy('user_id');
    }

    public function countFollowed()
    {
        return $this->hasOne(Follower::class, 'follower_user_id')
            ->selectRaw('follower_user_id, count(*) AS count')
            ->notDeleted()
            ->groupBy('follower_user_id');
    }

    // Scopes
    public function scopeMe($query)
    {
        return $query->where('id', self::myId());
    }

    public function scopeWhereInIds($query, $ids)
    {
        return $ids == null
            ? $query
            : $query->whereIn('id', $ids);
    }

    public function scopeNotDeleted($query)
    {
        return $query->where('is_deleted', false);
    }

    public function scopeDeleted($query)
    {
        return $query->where('id_deleted', true);
    }

    public function scopeEnabled($query)
    {
        return $query->where('is_enable', true);
    }

    public function scopeNotEnabled($query)
    {
        return $query->where('is_enable', false);
    }

    public function scopeMy($query)
    {
        return $query->where('user_id', User::myId());
    }

    public function scopeSearch($query, $search)
    {
        return $search
            ? $query->where('name', 'LIKE', $search.'%')
            : $query;
    }

    public function scopeWithAll($query)
    {
        return $query->with(['role', 'image', 'countFollow', 'countFollowed']);
    }
}
