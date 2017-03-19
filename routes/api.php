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
    //Auth API (todo logout)
    $api->post('users/login', 'App\Http\Controllers\Auth\LoginController@authenticate');
    $api->post('users/register', 'App\Http\Controllers\Auth\RegisterController@postRegister');
});

$api->version('v1', ['middleware' => 'api.auth'], function ($api) {
    // Auth API
    $api->get('token', 'App\Http\Controllers\Auth\LoginController@getToken');

    // User API
    $api->get('users', 'App\Http\Controllers\Api\V1\UserController@index');
    $api->get('user', 'App\Http\Controllers\Api\V1\UserController@show');
    // following system
    $api->get('user/followers/', 'App\Http\Controllers\Api\V1\UserController@getUserFollowers');
    $api->get('user/follow/', 'App\Http\Controllers\Api\V1\UserController@followUser');
    $api->get('user/unfollow/', 'App\Http\Controllers\Api\V1\UserController@unfollowUser');
    // editing
    $api->post('user/avatar', 'App\Http\Controllers\Api\V1\UserController@updateAvatar');
    //songs
    $api->get('user/songs/', 'App\Http\Controllers\Api\V1\UserController@getUserSongs');

    // Song API
    $api->get('songs', 'App\Http\Controllers\Api\V1\SongController@getSongs');
    $api->post('songs/add', 'App\Http\Controllers\Api\V1\SongController@uploadSong');
});
