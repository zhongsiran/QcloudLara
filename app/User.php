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
        'user_name', 'user_real_name', 'password', 'slaic_openid', 'scjg_openid', 'active_status', 'mode'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token', 'password',
    ];

    public function corp_photos()
    {
        return $this->hasMany('App\CorpPhotos', 'uploader', 'slaic_openid');
    }

}
