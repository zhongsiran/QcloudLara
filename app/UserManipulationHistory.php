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
}
