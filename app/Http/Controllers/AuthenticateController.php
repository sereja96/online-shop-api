<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticateController extends Controller
{
    public static function index()
    {

    }

    public static function authenticate(Request $request)
    {
        $credentials = $request->only(['login', 'password']);

        try {
            // verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'status' => 'error',
                    'message' => trans('messages.invalid_credentials')
                ], 200);
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json([
                'status' => 'error',
                'message' => trans('messages.could_not_create_token')
            ], 500);
        }

        $currentUser = User::with(['role', 'image'])
            ->where('id', Auth::user()->id)
            ->first();

        // if no errors are encountered we can return a JWT
        return response()->json([
            'status' => 'success',
            'token' => $token,
            'user' => $currentUser
        ], 200);
    }

    public static function _authenticate($credentials)
    {

        try {
            // verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'status' => 'error',
                    'message' => trans('messages.invalid_credentials')
                ], 200);
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json([
                'status' => 'error',
                'message' => trans('messages.could_not_create_token')
            ], 500);
        }

        $currentUser = User::with(['role', 'image'])
            ->where('id', Auth::user()->id)
            ->first();

        // if no errors are encountered we can return a JWT
        return response()->json([
            'status' => 'success',
            'token' => $token,
            'user' => $currentUser
        ], 200);
    }

    public static function logout()
    {
        Auth::logout();
        return response()->json(["status" => "success"], 200);
    }
}
