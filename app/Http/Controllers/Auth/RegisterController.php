<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;

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

    protected function validator(array $data)
    {
        //TODO: https://laravel.com/docs/5.4/validation#available-validation-rules
        $messages = [
            'required' => 'Поле :attribute є обов\'язковим.',
        ];
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
        ], $messages);
    }

    protected function postRegister(Request $request)
    {
        $parameters = $request->only('name', 'email', 'password');
        $validator = $this->validator($parameters);
        if ($validator->fails()) {
            $error = $validator->errors()->all();
            return $this->response->array(compact('error'))->setStatusCode(400);
        }
        return User::create([
            'name' => $parameters['name'],
            'email' => $parameters['email'],
            'password' => Hash::make($parameters['password']),
        ]);
    }
}
	