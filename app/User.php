<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function followers()
    {
        $followers = \DB::table('follows')->where('follower',$this->id)->count();

        return $followers;
    }

    public function following()
    {
        $followers = \DB::table('follows')->where('followed',$this->id)->count();

        return $followers;
    }

    public function projects_count()
    {
        $projects_count = Projects::where('user_id',$this->id)->count();
        return $projects_count;
    }
}
