<?php

namespace App\Http\Controllers;

use App\Folder;
use App\Follower;
use App\Media;
use App\Role;
use App\User;
use App\UserSettings;
use App\WishSettings;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;

use App\Http\Requests;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public static function getProfile()
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json([
                    'status' => 'error',
                    'message' => trans('messages.not_found', ['item' => trans('model.user')])
                ], 200);
            }

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'token_expired'
            ], $e->getStatusCode());

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'token_invalid'
            ], $e->getStatusCode());

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'token_absent'
            ], $e->getStatusCode());

        }

        // the token is valid and we have found the user via the sub claim
        return UserController::getUser($user->id);
    }

    public static function getUser($id)
    {
        $user = User::with(
            [
                'role',
                'image',
                'city'
            ])
            ->where('id', $id)
            ->first();

        if ($user) {
            return response()->json([
                'status' => 'success',
                'data' => $user
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => trans('messages.not_found', ['item' => trans('model.user')])
            ], 200);
        }
    }

    private static function getUsersFriends($users)
    {
        if (!$users || !count($users)) {
            return response()->json([
                'status' => 'error',
                'message' => trans('messages.not_found', ['item' => trans('model.user')])
            ], 200);
        } else {
            $usersIFollowed = Follower::where('follower_user_id', Auth::user()->id)
                ->select('user_id AS id', 'status')
                ->get();

            $newUsersArray = [];
            if ($usersIFollowed) {
                foreach ($users as $user)
                {
                    foreach ($usersIFollowed as $friend)
                    {
                        if ($user->id == $friend->id) {
                            $user->my_follow_status = $friend->status;
                            break;
                        }
                    }
                    array_push($newUsersArray, $user);
                }
            } else {
                foreach ($users as $user)
                {
                    $user->my_follow_status = null;
                    array_push($newUsersArray, $user);
                }
            }

            return response()->json([
                'status' => 'success',
                'data' => $newUsersArray
            ], 200);
        }
    }

    public static function getAllUsers()
    {
        $users = User::with('image')
            ->where('is_deleted', false)
            ->where('is_enable', true)
            ->get();

        return UserController::getUsersFriends($users);
    }

    public static function searchVkUsers()
    {
        if (Input::has('vk_ids')) {
            $vkIds = Input::get('vk_ids');
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'unknown_error'
            ], 200);
        }

        $users = User::with('image')
            ->whereIn('vk_id', $vkIds)
            ->where('is_deleted', false)
            ->where('is_enable', true)
            ->get();

        return UserController::getUsersFriends($users);
    }

    public static function searchUsers($search)
    {

    }

    public static function deleteProfile()
    {
        $user = User::find(Auth::user()->id);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => trans('messages.not_found', ['item' => trans('model.user')])
            ], 200);
        } else {
            $user->is_deleted = true;
            $user->save();

            return response()->json([
                'status' => 'success',
            ], 200);
        }
    }

    public static function restoreProfile()
    {
        $user = User::find(Auth::user()->id);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => trans('messages.not_found', ['item' => trans('model.user')])
            ], 200);
        } else {
            $user->is_deleted = false;
            $user->save();

            return response()->json([
                'status' => 'success',
            ], 200);
        }
    }

    private static function checkValidData($dataArray, &$errors) {
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

    public static function registration()
    {
        $data = Input::all();

        if (isset($data['vk_auth']) && $data['vk_auth']) {

            $vk_id = isset($data['uid']) && $data['uid']
                ? $data['uid']
                : null;

            if (!$vk_id) {
                return response()->json([
                    "status" => "error",
                    "message" => "invalid_vk_id"
                ], 200);
            }

            $findUser = User::where('vk_id', $vk_id)->first();
            if ($findUser) {
                return AuthenticateController::_authenticate([
                    'login' => $findUser['login'],
                    'password' => 'vk_secret.password.wish-HolidayList'
                ]);
            }

            $imageLastInsertedID = null;
            if (!empty($data['photo_200_orig'])) {
                $media = Media::create([
                    'link' => $data['photo_200_orig']
                ]);
                $imageLastInsertedID = $media->id;
            }

            $userResult = [
                'first_name' => $data['first_name'],
                'last_name' => !empty($data['last_name']) ? $data['last_name'] : null,
                'sex' => !empty($data['sex']) ? $data['sex'] : null,
                'timezone' => !empty($data['timezone']) ? $data['timezone'] : null,
                'vk_id' => $data['uid'],
                'bdate' => $data['bdate'],
                'media_id' => $imageLastInsertedID,
                'city_id' => !empty($data['city']) ? $data['city'] : null,
                'login' => $data['uid'],
                'password' => Hash::make('vk_secret.password.wish-HolidayList')
            ];

            User::create($userResult);
            return AuthenticateController::_authenticate([
                'login' => $data['uid'],
                'password' => 'vk_secret.password.wish-HolidayList'
            ]);

        } elseif (isset($data['standard_auth']) && $data['standard_auth']) {

            if (empty($data['first_name'])
                || empty($data['login'])
                || empty($data['password'])
                || empty($data['confirm'])) {

                return response()->json([
                    "status" => "error",
                    "message" => "invalid_data"
                ], 200);
            }

            if (!self::checkValidData(
                [
                    'LOGIN' => $data['login'],
                    'PASSWORD' => $data['password'],
                    'FIRST_NAME' => $data['first_name']
                ],
                $errors)) {

                return response()->json([
                    "status" => "error",
                    "message" => $errors
                ], 200);
            }

            if (User::where('login', $data['login'])->first()) {
                return response()->json([
                    "status" => "error",
                    "message" => "user_already_exists"
                ], 200);
            }

            if ($data['password'] !== $data['confirm']) {
                return response()->json([
                    "status" => "error",
                    "message" => "invalid_password_confirmation"
                ], 200);
            }

            $data['password'] = Hash::make($data['password']);
            $userResult = [
                'first_name' => $data['first_name'],
                'last_name' => isset($data['last_name']) ? $data['last_name'] : null,
                'login' => $data['login'],
                'password' => $data['password'],
            ];

            $user = User::create($userResult);

            return AuthenticateController::_authenticate([
                'login' => $user->login,
                'password' => $data['password']
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'registration_method_not_found'
            ], 200);
        }
    }

    public static function editProfile()
    {
        return response()->json([
            'status' => 'ENDPOINT'
        ], 200);
    }

    public static function changePassword()
    {
        $data = Input::all();
        if (!isset($data['oldPassword'], $data['newPassword'], $data['confirm'])
            || strlen($data['oldPassword']) < 3
            || !strlen($data['newPassword']) < 3
            || !strlen($data['confirm']) < 3) {
            return response()->json([
                'status' => 'error',
                'message' => 'invalid_data'
            ], 200);
        }

        if (strcmp($data['newPassword'], $data['confirm'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'different_passwords'
            ], 200);
        }

        if (!strcmp($data['oldPassword'], $data['newPassword'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'old_password'
            ], 200);
        }

        $user = Auth::user();
        $user->password = Hash::make($data['newPassword']);
        if (!$user->saveOrFail()) {
            return response()->json([
                'status' => 'error',
                'message' => trans('messages.unknown_error')
            ], 200);
        }

        return response()->json([
            'status' => 'success'
        ], 200);
    }

    public static function restorePassword($email)
    {
        $user = User::where('email', $email)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => trans('messages.not_found', ['item' => trans('model.email')])
            ], 200);
        }

        return response()->json([
            'status' => 'success'
        ], 200);
    }

    public static function getRoles()
    {
        $roles = Role::all();
        return response()->json([
            'status' => 'success',
            'data' => $roles
        ], 200);
    }
}
