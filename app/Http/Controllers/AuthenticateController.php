<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Response;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticateController extends Controller
{
    public function isProtected()
    {
        return Auth::check()
            ? Response::success()
            : Response::error();
    }

    public function createToken($credentials)
    {
        try {
            $token = JWTAuth::attempt($credentials);
        } catch (JWTException $e) {
            return false;
        }

        return $token;
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only(['login', 'password']);

        if ($token = $this->createToken($credentials)) {
            if ($currentUser = $this->getFullUser()) {
                return Response::success([
                    'token' => $token,
                    'user' => $currentUser
                ]);
            }
        }

        return Response::error('token_not_created');
    }

    public function logout()
    {
        Auth::logout();
        return Response::success();
    }

    private function getFullUser()
    {
        $userController = new UserController();
        return Auth::check()
            ? $userController->getUserById(User::myId())
            : null;
    }
}
