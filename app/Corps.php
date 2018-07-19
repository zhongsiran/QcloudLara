<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Corps extends Model
{
    public $keyType = 'string';
    public $primaryKey = 'registration_num';
    public $incrementing  = false;

    protected $guarded = [];
}