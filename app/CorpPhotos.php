<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CorpPhotos extends Model
{
    protected $casts = [
        'special_actions' => 'collection'
    ];

    protected $guarded= [];
}
