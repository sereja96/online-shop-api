<?php

namespace App;


class Response
{
    const SUCCESS = 'success';
    const ERROR = 'error';

    public static function success($data = null)
    {
        $response = ['status' => self::SUCCESS];
        if ($data) {
            $response = array_merge($response, [
                'data' => $data
            ]);
        }

        return response()->json($response, 200);
    }

    public static function error($message = null, $data = null)
    {
        $response = ['status' => self::ERROR];

        if ($message) {
            $response = array_merge($response, [
                'message' => $message
            ]);
        }

        if ($data) {
            $response = array_merge($response, [
                'data' => $data
            ]);
        }

        return response()->json($response, 200);
    }
}