<?php

namespace App\Http\Controllers\Api\V1;

use App\Song;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;

class SongController extends Controller
{
    protected function validator(array $data)
    {
        //TODO: https://laravel.com/docs/5.4/validation#available-validation-rules
        $messages = [
            'required' => 'Поле :attribute є обов\'язковим.',
        ];
        return Validator::make($data, [
            'Song_Name' => 'max:255',
            'Song_Description' => 'max:1000',
            'Song_File' => 'required|mimes:audio/mpeg,audio/mp3,audio/mpeg3,mpga|max:10000',
        ], $messages);
    }

    public function uploadSong(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            $this->response->errorUnauthorized('Token is invalid');
        }
        $song = new Song();


        $parameters = $request->only('name', 'description', 'song');
        $parameters = array(
            'Song_Name' => $parameters['name'],
            'Song_Description' => $parameters['description'],
            'Song_File' => $parameters['song']
        );

        $validator = $this->validator($parameters);
        if ($validator->fails()) {
            $error = $validator->errors()->all();
            return $this->response->array(compact('error'))->setStatusCode(400);
        }

        $filename = time() . '.' . $parameters['Song_File']->getClientOriginalExtension();
        $location = storage_path('app/users/' . $user->Person_ID . '/music/');
        $parameters['Song_File']->move($location, $filename);
        $song->Song_File = $filename;
        $parameters = $request->only('name', 'description');
        $song->Song_Name = $parameters['name'];
        $song->Song_Description = $parameters['description'];
        $song->save();
        return $song;
    }

    public function getSongs()
    {
        return Song::all();
    }
}
