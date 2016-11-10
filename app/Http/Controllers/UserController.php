<?php

namespace App\Http\Controllers;

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

        $currentUser = null;
        if (!$errorMessage) {
            $currentUser = User::getUser($user->id);
            if (!$currentUser) {
                $errorMessage = trans('messages.not_found', ['item' => trans('model.user')]);
            }
        }

        return $errorMessage
            ? Response::error($errorMessage)
            : Response::success($currentUser);
    }

    public function getAllUsers()
    {
        $users = User::searchUsers();
        return Response::success($users);
    }

    public function deleteProfile()
    {
        if ($errorMessage = Auth::user()->updateDeleted(true)) {
            return Response::error($errorMessage);
        }

        return Response::success();
    }

    public function getUser($id)
    {
        if ($user = User::getUser($id)) {
            return Response::success($user);
        }

        return Response::error(trans('messages.not_found', ['item' => trans('model.user')]));
    }

    public function restoreProfile()
    {
        if ($errorMessage = Auth::user()->updateDeleted(false)) {
            return Response::error($errorMessage);
        }

        return Response::success();
    }

    public function registration()
    {
        $data = Input::all();
        $errorMessage = null;

        if (isset($data['standard_auth']) && $data['standard_auth']) {

            if (empty($data['first_name'])
                || empty($data['login'])
                || empty($data['password'])
                || empty($data['confirm'])) {

                return Response::error('invalid_data');
            }

            $errors = [];
            if (!User::checkValidData([
                    'LOGIN' => $data['login'],
                    'PASSWORD' => $data['password'],
                    'FIRST_NAME' => $data['first_name']
                ], $errors)) {
                return Response::error($errors);
            }

            if (User::isExistsLogin($data['login'])) {
                return Response::error('user_already_exists');
            }

            if ($data['password'] !== $data['confirm']) {
                return Response::error('invalid_password_confirmation');
            }

            $data['password'] = Hash::make($data['password']);
            $userResult = [
                'first_name' => $data['first_name'],
                'last_name' => isset($data['last_name']) ? $data['last_name'] : null,
                'login' => $data['login'],
                'password' => $data['password'],
            ];

            $user = User::create($userResult);

            if ($token = User::auth([
                'login' => $user->login,
                'password' => $data['password']
            ])) {
                $user = User::getUser(User::myId());
                return Response::success([
                    'token' => $token,
                    'user' => $user
                ]);
            } else {
                return Response::error('token_not_created');
            }
        }

        return Response::error('registration_method_not_found');
    }

    public function editProfile()
    {
        return Response::success();
    }

}
