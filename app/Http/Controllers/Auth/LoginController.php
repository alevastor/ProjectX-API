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

        return $this->response->array(compact('token'))->setStatusCode(200);
    }

    public function index()
    {
        return \App\User::all();
    }

    public function show()
    {
        try {
            $user = JWTAuth::parseToken()->toUser();
            if (!$user) {
                $this->response->errorNotFound("User not found!");
            }
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $ex) {
            return $this->response->error('Something went wrong');
        }

        return $this->response->array(compact('user'));//->setStatusCode(200));
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

    public function destroy()
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            // fail the delete process
            return 'failed to delete user';
        }

        //delete the user
        return 'user \'' . $user->name . '\' will be deleted';
        //$user->delete();
    }

    // TODO: get user_id from param, default is current user
    public function getUserFollowers()
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            $this->response->errorUnauthorized('Token is invalid');
        }
        $followers = $user->followers;
        return $this->response->array(compact('followers'))->setStatusCode(200);
    }

    public function followUser(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            $this->response->errorUnauthorized('Token is invalid');
        }
        $parameters = $request->only('user_id');
        $user2 = \App\User::where('Person_ID', intval($parameters['user_id']))->first();
        if (!$user2) {
            $this->response->errorNotFound('User with such id not found');
        }
        if($user2 == $user)
        {
            return response()->json(['response' => -1], 403);
        }
        if(!$user2->followers->where('Person_ID', intval($user['Person_ID']))->first())
        {
            $user->follow($user2);
            return response()->json(['response' => 1], 200);
        }
        else
        {
            return response()->json(['response' => 4], 200);
        }
    }

    public function unfollowUser(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            $this->response->errorUnauthorized('Token is invalid');
        }
        $parameters = $request->only('user_id');
        $user2 = \App\User::where('Person_ID', intval($parameters['user_id']))->first();
        if (!$user2) {
            $this->response->errorNotFound('User with such id not found');
        }
        if($user2 == $user)
        {
            return response()->json(['response' => -1], 403);
        }
        if(!$user2->followers->where('Person_ID', intval($user['Person_ID']))->first())
        {
            return response()->json(['response' => -1], 404);
        }
        else
        {
            $user->unfollow($user2);
            return response()->json(['response' => 1], 200);
        }
    }
}
