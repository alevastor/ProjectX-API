<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    protected $table = "Songs";
    public $timestamps = false;
    protected $primaryKey = 'Song_ID';

    protected $fillable = [
        'Song_Name', 'Song_Description'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'Person_ID');
    }
}
