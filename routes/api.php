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

    $api->post('login', 'App\Http\Controllers\Auth\LoginController@authenticate');
});

$api->version('v1', ['middleware' => 'api.auth'], function ($api) {
    $api->get('users', 'App\Http\Controllers\Auth\LoginController@index');

    $api->get('user', 'App\Http\Controllers\Auth\LoginController@show');

    $api->get('token', 'App\Http\Controllers\Auth\LoginController@getToken');

    $api->get('user/delete', 'App\Http\Controllers\Auth\LoginController@destroy');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
