<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function index()
    {
        return \App\User::all();
    }

    //user_id
    public function show(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            $this->response->errorUnauthorized('Token is invalid');
        }
        $parameters = $request->only('user_id');
        if ($parameters['user_id'] != null) {
            $user = \App\User::where('Person_ID', intval($parameters['user_id']))->first();
            if (!$user) {
                $this->response->errorNotFound('User with such id not found');
            }
            return $this->response->array(compact('user'))->setStatusCode(200);
        }
        else {
            return $this->response->array(compact('user'))->setStatusCode(200);
        }
    }

    //user_id
    public function getUserFollowers(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            $this->response->errorUnauthorized('Token is invalid');
        }
        $parameters = $request->only('user_id');
        if ($parameters['user_id'] != null) {
            $user2 = \App\User::where('Person_ID', intval($parameters['user_id']))->first();
            if (!$user2) {
                $this->response->errorNotFound('User with such id not found');
            }
            $followers = $user2->followers;
            return $this->response->array(compact('followers'))->setStatusCode(200);
        } else {
            $followers = $user->followers;
            return $this->response->array(compact('followers'))->setStatusCode(200);
        }
    }

    //user_id
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
        if ($user2 == $user) {
            return response()->json(['response' => -1], 403);
        }
        if (!$user2->followers->where('Person_ID', intval($user['Person_ID']))->first()) {
            $user->follow($user2);
            return response()->json(['response' => 1], 200);
        } else {
            return response()->json(['response' => 4], 200);
        }
    }

    // user_id
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
        if ($user2 == $user) {
            return response()->json(['response' => -1], 403);
        }
        if (!$user2->followers->where('Person_ID', intval($user['Person_ID']))->first()) {
            return response()->json(['response' => -1], 404);
        } else {
            $user->unfollow($user2);
            return response()->json(['response' => 1], 200);
        }
    }

    public function updateAvatar(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            $this->response->errorUnauthorized('Token is invalid');
        }
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $filename = time() . '.' . $avatar->getClientOriginalExtension();
            // Delete current image before uploading new image
            if ($user->Person_Avatar !== 'default.jpg') {
                $file = storage_path('app/public/avatars/' . $user->Person_Avatar);
                if (File::exists($file)) {
                    unlink($file);
                }
            }
            Image::make($avatar)->fit(300, 300)->save(storage_path('app/public/avatars/' . $filename));
            $user->Person_Avatar = $filename;
            $user->save();
            return $this->response->array(compact('user'))->setStatusCode(200);
        } else {
            return response()->json(['response' => -1], 400);
        }
    }

    public function getUserSongs(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            $this->response->errorUnauthorized('Token is invalid');
        }
        $parameters = $request->only('user_id');
        if ($parameters['user_id'] != null) {
            $user = \App\User::where('Person_ID', intval($parameters['user_id']))->first();
            if (!$user) {
                $this->response->errorNotFound('User with such id not found');
            }
            return $user->songs;
        } else {
            return $user->songs;
        }
    }
}
