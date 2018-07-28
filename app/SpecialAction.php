<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SpecialAction extends Model
{
    public function index()
    {
        return $this->select('sp_num', 'sp_name', 'created_at')->distinct()->orderBy('created_at', 'Desc')->get();
    }

    public function count($sp_num)
    {
        return $this->where('sp_num', $sp_num)->get()->count();
    }

    public function finish_count($sp_num)
    {
        return $this->where('sp_num', $sp_num)
                    ->where('finish_status', 'finished')
                    ->get()
                    ->count();
    }

    public function corp()
    {
        return $this->belongsTo('App\Corps', 'registration_num', 'registration_num');
    }
}
