<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\RegistersUsers;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    protected function postRegister(Request $request)
    {
        $parameters = $request->only('login', 'email', 'password', 'name', 'surname', 'description', 'avatar');
        $parameters = array(
            'Person_Login' => $parameters['login'],
            'Person_Email' => $parameters['email'],
            'Person_Password' => $parameters['password'],
            'Person_FirstName' => $parameters['name'],
            'Person_LastName' => $parameters['surname'],
            'Person_Description' => $parameters['description'],
            'Person_Avatar' => $parameters['avatar']
        );

        $validator = $this->validator($parameters);
        if ($validator->fails()) {
            $error = $validator->errors()->all();
            return $this->response->array(compact('error'))->setStatusCode(400);
        }
        $filename = time() . '.' . $parameters['Person_Avatar']->getClientOriginalExtension();
        Image::make($parameters['Person_Avatar'])->fit(300, 300)->save(storage_path('app/public/avatars/' . $filename));
        $user = User::create([
            'Person_FirstName' => $parameters['Person_FirstName'],
            'Person_LastName' => $parameters['Person_LastName'],
            'Person_Login' => $parameters['Person_Login'],
            'Person_Email' => $parameters['Person_Email'],
            'Person_Description' => $parameters['Person_Description'],
            'Person_Password' => Hash::make($parameters['Person_Password']),
            'Person_Avatar' => $filename,
        ]);
        if ($user != null) {
            Storage::makeDirectory('users/' . $user->Person_ID . '/music');
            Storage::makeDirectory('users/' . $user->Person_ID . '/images');
        }
        try {
            if (!$token = JWTAuth::attempt(["Person_Email" => $parameters['Person_Email'], "password" => $parameters['Person_Password']])) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $ex) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        return $this->response->array(compact('user') + compact('token'))->setStatusCode(200);
    }

    protected function validator(array $data)
    {
        //TODO: https://laravel.com/docs/5.4/validation#available-validation-rules
        $messages = [
            'required' => 'Поле :attribute є обов\'язковим.',
        ];
        return Validator::make($data, [
            'Person_Login' => 'required|max:255|unique:Persons',
            'Person_Email' => 'required|email|max:255|unique:Persons',
            'Person_Password' => 'required|min:6',
            'Person_FirstName' => 'required|max:255',
            'Person_LastName' => 'required|max:255',
            'Person_Description' => 'max:255',
            'Person_Avatar' => 'mimes:jpeg,jpg,png,gif|max:10000',
        ], $messages);
    }
}
	