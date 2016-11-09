<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use App\Models\Media;
use App\Models\Role;
use App\Models\User;
use App\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;

use App\Http\Requests;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProfile()
    {
        $errorMessage = null;
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                $errorMessage = trans('messages.not_found', ['item' => trans('model.user')]);
            }
        } catch (TokenExpiredException $e) {
            $errorMessage = 'token_expired';
        } catch (TokenInvalidException $e) {
            $errorMessage = 'token_invalid';
        } catch (JWTException $e) {
            $errorMessage = 'token_absent';
        }

        return $errorMessage
            ? Response::error($errorMessage)
            : UserController::getUser($user->id);
    }

    public function getUser($id)
    {
        $user = User::with(['role', 'image', 'city'])
            ->where('id', $id)
            ->first();

        return !!$user
            ? Response::success($user)
            : Response::error(trans('messages.not_found', ['item' => trans('model.user')]));
    }

    public function getAllUsers()
    {
        $users = User::with('image')
            ->where('is_deleted', false)
            ->where('is_enable', true)
            ->get();

        return Response::success($users);
    }

    public function deleteProfile()
    {
        $user = User::find(User::myId());

        if (!$user) {
            return Response::error(trans('messages.not_found', ['item' => trans('model.user')]));
        } else {
            $user->is_deleted = true;
            $user->save();

            return Response::success();
        }
    }

    public function restoreProfile()
    {
        $user = User::find(User::myId());

        if (!$user) {
            return Response::error(trans('messages.not_found', ['item' => trans('model.user')]));
        } else {
            $user->is_deleted = false;
            $user->save();

            return Response::success();
        }
    }

    private function checkValidData($dataArray, &$errors) {
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

    public function registration()
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

    public function editProfile()
    {
        return response()->json([
            'status' => 'ENDPOINT'
        ], 200);
    }

    public function changePassword()
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

    public function restorePassword($email)
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

    public function getRoles()
    {
        $roles = Role::all();
        return response()->json([
            'status' => 'success',
            'data' => $roles
        ], 200);
    }
}
