<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $credentials = array(
            'Person_Email' => $credentials['email'],
            'Person_Password' => $credentials['password']
        );

        try {
            //if (!$token = JWTAuth::attempt($credentials)) {
            if (!$token = JWTAuth::attempt(["Person_Email" => $credentials['Person_Email'], "password" => $credentials['Person_Password']])) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $ex) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        $user = JWTAuth::setToken($token)->authenticate();
        return $this->response->array(compact('user') + compact('token'))->setStatusCode(200);
    }

    public function getToken()
    {
        $token = JWTAuth::getToken();

        if (!$token) {
            $this->response->errorUnauthorized('Token is invalid');
        }

        try {
            $refreshedToken = JWTAuth::refresh($token);
        } catch (JWTException $ex) {
            $this->response->error('Something went wrong');
        }

        return $this->response->array(compact('refreshedToken'));
    }
}
