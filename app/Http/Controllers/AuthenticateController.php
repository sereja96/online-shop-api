<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Response;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

class AuthenticateController extends Controller
{
    public function isProtected()
    {
        return Auth::check()
            ? Response::success()
            : Response::error();
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only(['login', 'password']);

        if ($token = User::auth($credentials)) {
            $currentUser = User::getUser(User::myId());

            if ($currentUser) {
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
}
