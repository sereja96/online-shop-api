<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Response;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticateController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->only(['login', 'password']);
        $errorMessage = null;
        
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                $errorMessage = trans('messages.invalid_credentials');
            }
        } catch (JWTException $e) {
            $errorMessage = trans('messages.could_not_create_token');
        }

        $currentUser = User::with(['role', 'image'])
            ->me()
            ->first();

        if (!$currentUser) {
            $errorMessage = trans('messages.unknown_error');
        }

        if ($errorMessage) {
            return Response::error($errorMessage);
        }

        $data = [
            'token' => $token,
            'user' => $currentUser
        ];

        return Response::success($data);
    }

    public function logout()
    {
        Auth::logout();
        return Response::success();
    }
}
