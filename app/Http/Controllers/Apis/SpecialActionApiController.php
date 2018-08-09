<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Corps;
use App\CorpPhotos;
use App\Http\Resources\CorpPhotoCollection;
use App\SpecialAction;
use App\Http\Resources\SpecialActionCollection;

use Qcloud\Cos\Client;

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
            $data['msg'] ='保存失败m';
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
        $data['msg'] = '成功保存';
        return $data;
    }

    public function general_upload_photo(Request $request, SpecialAction $special_action, Corps $corp, CorpPhotos $corpPhotos, Client $cos_client) {
        $app = app('wechat.official_account');
        $token = $app->access_token->getToken();
        $token = $token['access_token'];
        $corporation_name = $request->corp['corporation_name'];
        $corporation_aic_division = $request->corp['corporation_aic_division'];
        $registration_num = $request->corp['registration_num'];
        $sp_name = $request->sp_item['sp_name'] ?? '日常监管';
        $sp_num = $request->sp_item['sp_num'] ?? null;
        $uploader = $request->uploader;
        $total_count = count($request->serverIds);
        $sucess_count = 0;
        $fail_count = 0;

        // return $registration_num;

        foreach ($request->serverIds as $server_id) {
            // return $server_id;
            $pic_url = "https://api.weixin.qq.com/cgi-bin/media/get?access_token=". $token . "&media_id=" . $server_id;
            // return $pic_url;

            $upload_timestring = \Carbon\Carbon::now()->format('Ymd-Hi--sv');

            $image_upload_name = sprintf("%s_%s_%s.jpg", 
            $corporation_aic_division, 
            $corporation_name,
            $upload_timestring);
            // return $image_upload_name;
            $full_key = 'CorpImg/' . $corporation_aic_division . '/'. $sp_name .'/' . date('Ymd'). '/' . $image_upload_name;
            // return $full_key;
            if($this->upload_image($full_key, $pic_url, $cos_client)) 
            {
                CorpPhotos::create([
                    'corporation_name' => $corporation_name,
                    'link' => $full_key,
                    'uploader' => $uploader,
                    'division' => $corporation_aic_division,
                    'special_actions' => $sp_num
                ]);
            
                $photos_number = CorpPhotos::where('corporation_name', $corporation_name)->count();
                Corps::find($registration_num)->update(['photos_number' => $photos_number]);
                $sucess_count += 1;
            }else {
                $fail_count += 1;
            }
        }
        return sprintf('共上传%d张照片，成功%d张，失败%d张', $total_count, $sucess_count, $fail_count);
    }

    public function upload_image($full_key, $photo_url, Client $cos_client)
    {
        // 要求提供content length的HTTP HEADER        
        $headers = get_headers($photo_url, true);
        $content_length = $headers['Content-Length'];

        try {
            $result = $cos_client->putObject(array(
                'Bucket' => config('qcloud.bucket'),
                'Key' =>  $full_key, // CorpImg/SL/日常监管/广州华伟广告设计有限公司/SL-1_2018-06-12.jpg 
                'Body' => fopen($photo_url, 'rb'),
                'ContentType' => 'image/jpeg', 
                'ContentLength' => $content_length,
                'ServerSideEncryption' => 'AES256'));
        } catch (\Exception $e) {
             return false;
        }
        return true;
    }
}
