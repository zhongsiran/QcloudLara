<?php 

namespace App\Utils;

use App\Corps;
use App\UserManipulationHistory as ManHistory;

use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Image;

use Qcloud\Cos\Api;

use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * WeChatAutoReplyFunctions
 */
trait WeChatAutoReplyTraits
{

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

    public function handle_image_message($message)
    {
        $pic_url = $message['PicUrl'];
        $media_id = $message['MediaId'];

        try {
            $history = ManHistory::findOrFail($message['FromUserName']);
            $history_registration_num = $history->current_manipulating_corporation;            
        } catch (ModelNotFoundException $e) {
            return '当前无指定操作企业，请先指定再上传定位';
        }
        try {
            $current_corporation = Corps::findOrFail($history_registration_num);
        } catch (ModelNotFoundException $e) {
            return '在数据库中无法找到当前操作企业，请重新指定要操作的企业。';
        }
        $corp_name = $current_corporation->corporation_name;
        $div = $current_corporation->corporation_aic_division;
        $date = date("Ymd-His");

        // $image_upload_name = sprintf("%s-%s-%s", $div, $current_pic_num, $date);
        // $return_image = new Image($media_id);
        return $this->list_objects_with_prefix('广东钱龙五金制品有限公司');
        // return $date;
    }

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

    public function get_corporation_info_by_name($keyword)
    {
        $corps_found = Corps::where('corporation_name', 'like', '%' .$keyword .'%')->take(20)->get();
        $result_string = '';
        $count = 1;
        if ($corps_found->count() > 0) {
            foreach ($corps_found as $corp) {
                $result_string .= 
                $count . ':' .$corp->corporation_name . "\n" . 
                $corp->registration_num . "\n".
                "------------------------". "\n";
                $count += 1;
            }
            return $result_string;
        }else{
            return '无法找到包含“' . $keyword .'”的企业';
        }
    }

    public function get_corporation_route_plan($registration_num)
    {
        try {
            $current_corporation = Corps::findOrFail($registration_num);
        } catch (ModelNotFoundException $e) {
            return '在数据库中无法找到当前操作企业，请重新指定要操作的企业。';
        }
        $latitude = $current_corporation->latitude;
        $longitude = $current_corporation->longitude;
        if (isset($latitude) && isset($longitude)) {
            $corporation_name = $current_corporation->corporation_name;
            $corporation_description = $registration_num . "\n" . $current_corporation->address;
            $url = "https://apis.map.qq.com/tools/routeplan/eword=". $corporation_name ."&epointx=".$longitude."&epointy=".$latitude."?referer=wxbro&key=6GJBZ-WKHKD-VBT4V-POM3Q-K3DW7-BJBL3";

            $items = [
                new NewsItem(['title'=> $corporation_name,
                    'description' => "点击显示导航路径",
                    'url'         => $url,
                    'image'       => 'http://hd-cloud-aic-1253807511.cosgz.myqcloud.com/routeplan.jpg',])
            ];
            $routeplan_links = new News($items);
            return $routeplan_links;
        } else {
            return "当前企业无定位记录";
        }
    }

    public function upload_image($fileName, $realPath)
    {
        $cosClient = new Qcloud\Cos\Client(array('region' => env('QCLOUD_REGION'),
            'credentials'=> array(
                'appId' => env('QCLOUD_APPID'),
                'secretId'    => env('QCLOUD_SECRETID'),
                'secretKey' => env('QCLOUD_SECRETKEY'))));
        try {
            $result = $cosClient->putObject(array(
                'Bucket' => env('QCLOUD_BUCKET'),
                'Key' =>  $fileName,
                'Body' => fopen($realPath, 'rb'),
                'ServerSideEncryption' => 'AES256'));
        } catch (\Exception $e) {
            echo "$e\n";
            echo '</br> 失败';
        }
        unset($cosClient);
    }

    public function list_objects_with_prefix($corp_name, $prefix='CorpImg/SL/日常监管/')
    {
        $cosClient = new \Qcloud\Cos\Client(array('region' => env('QCLOUD_REGION'),
            'credentials'=> array(
                'appId' => env('QCLOUD_APPID'),
                'secretId'    => env('QCLOUD_SECRETID'),
                'secretKey' => env('QCLOUD_SECRETKEY'))));
        if (isset($cosClient)) {
            return 'ok';
            # code...
        }else{
            return 'fail';
        }
        try {
            $result = $cosClient->listObjects(array(
                'Bucket' => 'aic-1253948304',
                'Prefix' => 'CorpImg/SL/日常监管/',
            ));
            // foreach ($result['Contents'] as $rt) {
            //     print_r($rt);
        // }
        } catch (\Exception $e) {
            return ($e);
        }
        return "OK";
    }
}