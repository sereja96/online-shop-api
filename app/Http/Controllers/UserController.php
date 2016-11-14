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
            $currentUser = $this->getUserById($user->id);
            if (!$currentUser) {
                $errorMessage = trans('messages.not_found', ['item' => trans('model.user')]);
            }
        }

        return $errorMessage
            ? Response::error($errorMessage)
            : Response::success($currentUser);
    }

    public function searchUsers($ids = null)
    {
        $users = User::withAll()
            ->whereInIds($ids)
            ->enabled()
            ->get();

        return $users;
    }

    public function getUserById($id)
    {
        $user = User::withAll()
            ->whereInIds($id)
            ->enabled()
            ->first();

        return $user;
    }

    public function getAllUsers()
    {
        $users = $this->searchUsers();
        return Response::success($users);
    }

    public function deleteProfile()
    {
        Auth::user()->delete();
        return Response::success();
    }

    public function restoreProfile()
    {
        if (Auth::user()->trashed()) {
            Auth::user()->restore();
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
            if (!$this->checkValidData([
                    'LOGIN' => $data['login'],
                    'PASSWORD' => $data['password'],
                    'FIRST_NAME' => $data['first_name']
                ], $errors)) {
                return Response::error($errors);
            }

            if ($this->isExistsLogin($data['login'])) {
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
            $credentials = [
                'login' => $user->login,
                'password' => $data['password']
            ];

            $authController = new AuthenticateController();
            if ($token = $authController->createToken($credentials)) {
                $user = $this->getUserById(User::myId());
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

    private function isExistsLogin($login)
    {
        return User::where('login', $login)
            ->first();
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

}
