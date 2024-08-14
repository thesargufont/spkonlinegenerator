<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\JsonResponseAPI;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginAPIController extends Controller {
    public function loginMobile(Request $request)
    {
        try {
            if(!isset($request->email, $request->password)) {
                return JsonResponseAPI::jsonResponseError('Incorrect Request', 400);
            }

            $data = [
                'email' => $request->email,
                'password' => $request->password
            ];
            
            if (!$token = JWTAuth::attempt($data)) {
                return JsonResponseAPI::jsonResponseError('Invalid Email or Password', 401);
            }

            return JsonResponseAPI::jsonResponseSuccessToken('Login successfully', $token, 200);
            
        } catch (\Exception $e) {
            return JsonResponseAPI::jsonResponseError($e->getMessage(), $e->getCode()); 
        }
    }

    public function getUser(Request $request) {
        try {
            if(!isset($request->email)) {
                return JsonResponseAPI::jsonResponseError('Incorrect Request', 400);
            }

            $email = $request->email;
            
            $findUser = User::where('email', $email)
                        ->first()
                        ->toArray();
            
            if($findUser) {
                return JsonResponseAPI::jsonResponseSuccess('Get User Successfully', $findUser);
            }
            
            return JsonResponseAPI::jsonResponseError('User Not Found', 404);
        } catch (\Exception $e) {
            return JsonResponseAPI::jsonResponseError($e->getMessage(), $e->getCode());
        }
    }
}
