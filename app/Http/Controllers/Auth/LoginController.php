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

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    //public function __construct()
    //{
    //    $this->middleware('guest', ['except' => 'logout']);
    //}

    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try
        {
            if(! $token = JWTAuth::attempt($credentials))
            {
                return $this->response->errorUnauthorized();
            }
        }
        catch (JWTException $ex)
        {
            return $this->response->errorInternal();
        }

        return $this->response->array(compact('token'))->setStatusCode(200);
    }

    public function index()
    {
        return \App\User::all();
    }

    public function show()
    {
        try
        {
            $user = JWTAuth::parseToken()->toUser();
            if(! $user)
            {
                $this->response->errorNotFound("User not found!");
            }
        }
        catch (\Tymon\JWTAuth\Exceptions\JWTException $ex)
        {
            return $this->response->error('Something went wrong');
        }

        return $this->response->array(compact('user'));//->setStatusCode(200));
    }

    public function getToken()
    {
        $token = JWTAuth::getToken();

        if(! $token)
        {
            $this->response->errorUnauthorized('Token is invalid');
        }

        try
        {
            $refreshedToken = JWTAuth::refresh($token);
        }
        catch (JWTException $ex)
        {
            $this->response->error('Something went wrong');
        }

        return $this->response->array(compact('refreshedToken'));
    }

    public function destroy()
    {
        $user = JWTAuth::parseToken()->authenticate();
        if(! $user)
        {
            // fail the delete process
            return 'failed to delete user';
        }

        //delete the user
        return 'user \''.$user->name.'\' will be deleted';
        //$user->delete();
    }
}
