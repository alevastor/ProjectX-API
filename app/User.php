<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use Notifiable, EntrustUserTrait;

    public $timestamps = false;
    protected $table = 'Persons';
    protected $primaryKey = 'Person_ID';
    protected $appends = ['followers_list'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'Person_Login', 'Person_Email', 'Person_Password', 'Person_LastName', 'Person_FirstName', 'Person_Description', 'Person_Avatar'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'Person_Password', 'Person_Email', 'pivot', 'followers',
    ];

    //костиль для JWT_Auth з нестандартним паролем
    public function getAuthPassword()
    {
        return $this->Person_Password;
    }

    function follow(User $user)
    {
        $user->followers()->attach($this->Person_ID);
    }

    function followers()
    {
        return $this->belongsToMany('App\User', 'followers', 'user_id', 'follower_id');
    }

    function unfollow(User $user)
    {
        $user->followers()->detach($this->Person_ID);
    }

    public function getFollowersListAttribute()
    {
        $array = [];
        foreach ($this->followers as $follower) {
            $follower->appends = [];
            array_push($array, $follower);
        }
        return $array;
    }
}
