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
    protected $appends = ['key'];

    public function getKeyAttribute()
    {
        return str_replace('https://aic-1253948304.cosgz.myqcloud.com/', '', $this->link);
    }
}
