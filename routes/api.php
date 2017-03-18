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
    $api->get('hello', 'App\Http\Controllers\HomeController@index');
    $api->get('users/{user_id}/roles/{role_name}', 'App\Http\Controllers\HomeController@attachUserRole');
    $api->get('users/{user_id}/roles', 'App\Http\Controllers\HomeController@getUserRole');

    $api->post('role/permission/add', 'App\Http\Controllers\HomeController@attachPermission');
    $api->get('role/{role_name}/permissions', 'App\Http\Controllers\HomeController@getPermissions');

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

    $api->get('user/delete', 'App\Http\Controllers\Auth\LoginController@destroy');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
