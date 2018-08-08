<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\SpecialAction;
use App\Http\Resources\SpecialActionCollection;
use App\CorpPhotos;
use App\Http\Resources\CorpPhotoCollection;

use Illuminate\Database\Eloquent\ModelNotFoundException;


class SpecialActionApiController extends Controller
{

    public function list_special_actions($division = null, SpecialAction $special_action) {  #  默认可查全部
        $division = \strtoupper($division);
        $special_action_list = $special_action->index($division);
        // return $division;
        return new SpecialActionCollection($special_action_list);
    }

    public function fetch_photo_links($division, $action_num = null, CorpPhotos $corpPhotos) {
        $division = \strtoupper($division); //保证大写
        if (is_null($action_num)) {
            $photo_items = $corpPhotos->where('division', $division)->where('special_actions', null)->get();
        }elseif ($action_num=='all') {
            $photo_items = $corpPhotos->where('division', $division)->get();
        }else {
            $photo_items = $corpPhotos->where('division', $division)->whereJsonContains('special_actions',  $action_num)->get();    
        }
        return new CorpPhotoCollection($photo_items);
    }

    public function update_special_item($id, Request $request)
    {
        try {
            $server_special_item = SpecialAction::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $data['msg'] ='cannot find the special item';
            return $data;
        }

        $attribute_list = [
            "finish_status",
            "start_inspect_time",
            "end_inspect_time",
            "inspection_record",
            "phone_call_record"
        ];
        // $server_special_item = $request->special_item;
        foreach ($attribute_list as $attribute) {
            $server_special_item->$attribute = $request->$attribute;
        }
        $server_special_item->save();
        $data['msg'] = 'save successfully';
        return $data;
    }
}
