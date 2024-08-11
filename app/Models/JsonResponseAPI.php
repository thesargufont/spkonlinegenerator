<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JsonResponseAPI extends Model {
    public static function jsonResponseSuccessToken($message = '', $token = '', $statusCode = 200) {
        return response()->json(
            [
                'success' => true,
                'message' => $message,
                'token' => $token
            ], $statusCode
        );
    }

    public static function jsonResponseSuccess($message = '', $data = null, $statusCode = 200) {
        return response()->json(
            [
                'success' => true,
                'message' => $message,
                'data' => $data
            ], $statusCode
        );
    }

    public static function jsonResponseError($message, $statusCode = 400) {
        return response()->json(
            [
                'success' => false,
                'message' => $message
            ], $statusCode
        );
    }
}