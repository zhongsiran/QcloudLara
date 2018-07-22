<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CorpPhotos extends Model
{
    use SoftDeletes;

    protected $casts = [
        'special_actions' => 'collection'
    ];

    protected $guarded= [];
    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo('App\User', 'uploader', 'slaic_openid');
    }

}
