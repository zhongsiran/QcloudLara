<?php 

namespace App\Utils;

use App\Corps;
use App\UserManipulationHistory as ManHistory;
use App\CorpPhotos;

use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Image;

use Qcloud\Cos\Client;

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
            $history_registration_num = ManHistory::findOrFail($message['FromUserName'])->current_manipulating_corporation;            
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
            $history_registration_num = ManHistory::findOrFail($message['FromUserName'])->current_manipulating_corporation;            
        } catch (ModelNotFoundException $e) {
            return '当前无指定操作企业，请先指定再上传照片';
        }
        try {
            $current_corporation = Corps::findOrFail($history_registration_num);
        } catch (ModelNotFoundException $e) {
            return '在数据库中无法找到当前操作企业，请重新指定要操作的企业。';
        }

        // 构造上传文件名为 所代号+企业名+日期毫秒.jpg
        $upload_timestring = \Carbon\Carbon::now()->format('Ymd-Hi--sv');
        $uploader = $message['FromUserName'];
        $image_upload_name = sprintf("%s_%s_%s.jpg", 
            $current_corporation->corporation_aic_division, 
            $current_corporation->corporation_name,
            $upload_timestring);

        // 命名规则:根目录/所代号/日常监管/年月/照片名称
        $full_key = 'CorpImg/' . $current_corporation->corporation_aic_division . '/日常监管/' . date('Ym'). '/' . $image_upload_name;  
        try {
            $result = $this->upload_image($full_key, $message['PicUrl']);
        } catch (\Exception $e) {
            return 'upload fail';
        }

        $upload_image_link = 'http://aic-1253948304.cosgz.myqcloud.com/'. \urlencode($full_key);
        CorpPhotos::create([
            'corporation_name' => $current_corporation->corporation_name,
            'link' => $upload_image_link,
            'uploader' => $message['FromUserName']
        ]);
        $photos_number = CorpPhotos::where('corporation_name', $current_corporation->corporation_name)->count();
        Corps::find($history_registration_num)->update(['photos_number' => $photos_number]);
        return '成功上传照片，当前共有' . $photos_number . '张照片';
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

    public function get_corporation_info_by_keyword($keyword, $type='corp_name')
    {
        switch ($type) {
            case 'corp_name':
                $column = 'corporation_name';
                $result_string = sprintf("名称包含'%s'的企业:\n", $keyword);
                break;
            case 'address':
                $column = 'address';
                $result_string = sprintf("地址包含'%s'的企业:\n", $keyword);
                break;
            case 'rep_person':
                $column = 'represent_person';
                $result_string = sprintf("法人包含'%s'的企业:\n", $keyword);
                break;
            
            default:
                $column = 'corporation_name';
                $result_string = sprintf("名称包含'%s'的企业:\n", $keyword);
                break;
        }
        $corps_found = Corps::where($column, 'like', '%' .$keyword .'%')->take(15)->get();
        $count = 1;
        if ($corps_found->count() > 0) {
            foreach ($corps_found as $corp) {
                $result_string .= 
                $count . ':' .$corp->corporation_name . "\n" . 
                $corp->registration_num . "\n".
                str_replace('广州市花都区狮岭镇', '', $corp->address) ."\n".
                $corp->represent_person . "\n".
                "------------------------". "\n";
                $count += 1;
            }
            return $result_string;
        }else{
            return '无法找到名称/地址中包含“' . $keyword .'”的企业';
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

    public function upload_image($full_key, $photo_url)
    {
         // CorpImg/SL/日常监管/广州华伟广告设计有限公司/SL-1_2018-06-12.jpg 
        
        $headers = get_headers($photo_url, true);
        $content_length = $headers['Content-Length'];

        try {
            $result = $this->cos_client->putObject(array(
                'Bucket' => config('qcloud.bucket'),
                'Key' =>  $full_key,
                'Body' => fopen($photo_url, 'rb'),
                'ContentType' => 'image/jpeg', 
                'ContentLength' => $content_length,
                'ServerSideEncryption' => 'AES256'));
        } catch (\Exception $e) {
             return 'fail';
        }
        return 'success';
    }

    public function list_objects_with_prefix($corp_name, $prefix='CorpImg/SL/日常监管')
    {
        try {
            $result = $this->cos_client->listObjects(array(
                'Bucket' => config('qcloud.bucket'),
                'Prefix' => $prefix . "/" . $corp_name,
            ));
        } catch (\Exception $e) {
            return 'fail';
        }
        return $result['Contents'][1]['Key'];

        /*object(Guzzle\Service\Resource\Model)#500 (2) { 
            ["structure":protected]=> NULL 
            ["data":protected]=> array(7) { 
                ["Name"]=> string(14) "aic-1253948304" 
                ["Prefix"]=> string(60) "CorpImg/SL/日常监管/广州华伟广告设计有限公司" 
                ["Marker"]=> array(0) { } 
                ["MaxKeys"]=> string(4) "1000" 
                ["IsTruncated"]=> bool(false) 
                ["Contents"]=> array(3) { 
                    [0]=> array(6) { 
                        ["Key"]=> string(80) "CorpImg/SL/日常监管/广州华伟广告设计有限公司/SL-1_2018-06-12.jpg" ["LastModified"]=> string(24) "2018-06-12T03:13:38.000Z" 
                        ["ETag"]=> string(42) ""1aa3a211dba82da8ec69e0692e5c66c62d0e5c3d"" 
                        ["Size"]=> string(6) "148804" 
                        ["Owner"]=> array(2) { 
                            ["ID"]=> string(10) "1253948304" 
                            ["DisplayName"]=> string(10) "1253948304" 
                            } 
                        ["StorageClass"]=> string(8) "STANDARD" 
                        } 
                    [1]=> array(6) { ["Key"]=> string(80) "CorpImg/SL/日常监管/广州华伟广告设计有限公司/SL-2_2018-06-12.jpg" ["LastModified"]=> string(24) "2018-06-12T03:13:40.000Z" ["ETag"]=> string(42) ""fef817a797b6168f5b53b46d6d01ed659e5df332"" ["Size"]=> string(6) "197058" ["Owner"]=> array(2) { ["ID"]=> string(10) "1253948304" ["DisplayName"]=> string(10) "1253948304" } ["StorageClass"]=> string(8) "STANDARD" } [2]=> array(6) { ["Key"]=> string(80) "CorpImg/SL/日常监管/广州华伟广告设计有限公司/SL-3_2018-06-12.jpg" ["LastModified"]=> string(24) "2018-06-12T03:13:41.000Z" ["ETag"]=> string(42) ""1aa3a211dba82da8ec69e0692e5c66c62d0e5c3d"" ["Size"]=> string(6) "148804" ["Owner"]=> array(2) { ["ID"]=> string(10) "1253948304" ["DisplayName"]=> string(10) "1253948304" } ["StorageClass"]=> string(8) "STANDARD" } 
                } 
                ["RequestId"]=> string(40) "NWI0ZjY5ODhfNjBhYTk0MGFfOTkzM181NDEyMw==" } }*/
    }
}