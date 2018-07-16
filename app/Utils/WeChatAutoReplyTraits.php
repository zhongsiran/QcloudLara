<?php 

namespace App\Utils;

use App\Corps;
use App\UserManipulationHistory as ManHistory;

/**
 * WeChatAutoReplyFunctions
 */
trait WeChatAutoReplyTraits
{
    public function fetch_corp_info(Corps $corp)
    {
        //用sprintf会保留换行和空格，为了代码易读，在书写时保持缩进，用str_replace将空格删除。不需要\n换行
        $corp_info_template = str_replace(' ','','
            %s
            %s
            地址：%s
            法人：%s
            电话：%s
            联络员：%s
            联络员电话：%s
            年报情况：%s
            核查记录：%s
            电话联系记录：%s
            相关图片数：%s
            ====================');
        return sprintf($corp_info_template,  // 模板
                //数据
            $corp->registration_num, 
            $corp->corporation_name,
            $corp->address,
            $corp->represent_person,
            $corp->phone,
            $corp->contact_person,
            $corp->contact_phone,
            $corp->nian_bao_status,
            $corp->inspection_status,
            $corp->phone_call_record,
            $corp->photos_number
        );
    }

    public function handle_location_message(array $message)
    {
        $latitude = $message['Location_X'];
        $longitude = $message['Location_Y'];

        try {
            $history = ManHistory::findOrFail($message['FromUserName']);
            $history_registration_num = $history->current_manipulating_corporation;            
        } catch (ModelNotFoundException $e) {
            return '当前无指定操作企业，请先指定再上传定位';
        }

        try {
            $current_corporation = Corps::where('registration_num', $history_registration_num)->firstOrFail();
            $current_corporation->latitude = $latitude;
            $current_corporation->longitude = $longitude;
            $current_corporation->save();
            return sprintf('成功上传定位信息，当前定位：东经 %s，北纬： %s', $latitude, $longitude);
        } catch (ModelNotFoundException $e) {
            return '在数据库中找不到当前操作企业，无法上传定位信息';
        }
    }
}