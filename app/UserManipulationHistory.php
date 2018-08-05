<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserManipulationHistory extends Model
{
    protected $fillable = [
        'id',
        'wx_nickname',
        'current_manipulating_corporation',
        'previous_manipulated_corporation',
    ];

    public static function clear($user_id)
    {
        $user = static::findOrFail($user_id);
        $user->current_manipulating_corporation = '';
        $user->previous_manipulated_corporation = '';
        $user->save();
    }
}
