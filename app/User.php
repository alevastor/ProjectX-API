<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use Notifiable, EntrustUserTrait;

    protected $table = 'Persons';
    public $timestamps = false;
    protected $primaryKey = 'Person_ID';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'Person_Login', 'Person_Email', 'Person_Password', 'Person_LastName', 'Person_FirstName', 'Person_Description',
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

    function followers()
    {
        return $this->belongsToMany('App\User', 'followers', 'user_id', 'follower_id');
    }

    function follow(User $user)
    {
        $user->followers()->attach($this->Person_ID);
    }

    function unfollow(User $user)
    {
        $user->followers()->detach($this->Person_ID);
    }
}
