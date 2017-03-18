<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    $api->post('users/login', 'App\Http\Controllers\Auth\LoginController@authenticate');
    $api->post('users/register', 'App\Http\Controllers\Auth\RegisterController@postRegister');
});

$api->version('v1', ['middleware' => 'api.auth'], function ($api) {
    $api->get('users', 'App\Http\Controllers\Auth\LoginController@index');
    $api->get('user', 'App\Http\Controllers\Auth\LoginController@show');
    $api->post('user/avatar', 'App\Http\Controllers\Auth\LoginController@updateAvatar');
    $api->get('user/followers/', 'App\Http\Controllers\Auth\LoginController@getUserFollowers');
    $api->get('user/follow/', 'App\Http\Controllers\Auth\LoginController@followUser');
    $api->get('user/unfollow/', 'App\Http\Controllers\Auth\LoginController@unfollowUser');
    $api->get('token', 'App\Http\Controllers\Auth\LoginController@getToken');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
