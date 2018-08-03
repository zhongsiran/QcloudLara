<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\ModelNotFoundException;


class SpecialAction extends Model
{
    public static function index($user_aic_division = '')
    {
        return static::select('sp_num', 'sp_name', 'created_at')
        ->where('sp_aic_division', 'like', '%' . $user_aic_division . '%')
        ->distinct()->orderBy('created_at', 'Desc')->get();
    }

    public static function count($sp_num)
    {
        return static::where('sp_num', $sp_num)->get()->count();
    }

    public static function finish_count($sp_num)
    {
        return static::where('sp_num', $sp_num)
        ->where('finish_status', 'finished')
        ->get()
        ->count();
    }

    public static function sp_name($division, $sp_num)
    {
        return static::where('sp_aic_division', $division)->where('sp_num', $sp_num)->firstOrFail()->sp_name;
    }

    public static function sp_item($sp_num, $registration_num)
    {
        return static::where('sp_num', $sp_num)->where('registration_num', $registration_num)->firstOrFail();
    }

    public static function sp_item_by_id($division, $sp_num, $corp_id)
    {
        return static::where('sp_aic_division', $division)->where('sp_num', $sp_num)->where('sp_corp_id', $corp_id)->firstOrFail();
    }

    public function corp()
    {
        return $this->belongsTo('App\Corps', 'registration_num', 'registration_num');
    }
}
