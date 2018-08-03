<?php 

namespace App\Utils;

use App\Corps;
use App\CorpPhotos;
use App\SpecialAction;
use App\User;
use App\UserManipulationHistory as ManHistory;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;

use Qcloud\Cos\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;

// use EasyWeChat\Kernel\Messages\Message;

/**
 * WeChatAutoReplyFunctions
 */
trait WeChatSpecialActionTraits
{
    private function handle_text_message_special_action(array $message)
    {
        $keyword = trim($message['Content']);
        switch (true) {
            // 转换模式
            case(preg_match('/^模式|ms*/',$keyword)):
            $mode = preg_replace('/^模式|ms+[ ：:,，]*/', '', $keyword);
            switch ($mode) {
                // 转成日常模式
                case '日常':
                $this->current_user->mode = 'daily';
                try {
                    $this->current_user->save();
                    return '成功转换为日常监管模式';
                } catch (\Exception $e) {
                    return '转换模式出错';
                };
                break;
                // 转成扫描模式
                case '扫描':
                $this->current_user->mode = 'scanning';
                try {
                    $this->current_user->save();
                    return '成功转换为扫描模式';
                } catch (\Exception $e) {
                    return '转换模式出错';
                };
                break;
                // 转成专项行动模式
                default:
                try {
                    $s = SpecialAction::where('sp_num', $mode)->where('sp_aic_division', $this->current_user->user_aic_division)->firstOrFail();
                } catch (ModelNotFoundException $e) {
                    return '无此代号的专项行动，请核实。';
                    break;
                }
                try {
                    $this->current_user->mode = $mode;
                    $this->current_user->save();
                    return '成功转换为专项行动模式，当前行动：' . $s->sp_num. ':' . $s->sp_name;
                } catch (\Exception $e) {
                    return '转换模式出错';
                };
                break;
            }
            break;
            // 收到“进入”之后回复链接页面
            case (strstr($keyword,'进入') or strstr($keyword, 'jr')):
            $title = '微信监管平台';
            $url = 'https://hdscjg.applinzi.com/mylib/H5controllers/shilingaic_openid.php';
            $image = 'http://sinacloud.net/aicbucket/babb0823bf2de393cbb694b1c7a71964.jpg';

            $items = [
                new NewsItem([
                    'title'       => $title,
                    'description' => "进入网页版监管平台",
                    'url'         => $url,
                    'image'       => $image,
                ]),
            ];
            $link_to_hd_cloud_aic_h5_website = new News($items);
            return $link_to_hd_cloud_aic_h5_website;
            break;

            case (strstr($keyword,'新进') or strstr($keyword, 'xj')):
            $title = '微信监管平台';
            $url = 'https://www.shilingaic.cn/index.php/platform/login?openid=' .$message['FromUserName'];
            $image = 'http://sinacloud.net/aicbucket/babb0823bf2de393cbb694b1c7a71964.jpg';

            $items = [
                new NewsItem([
                    'title'       => $title,
                    'description' => "进入网页版监管平台",
                    'url'         => $url,
                    'image'       => $image,
                ]),
            ];
            $platform_link = new News($items);
            return $platform_link;
            break;

            /*
             * 收到“当前”之后
             * 回复当前操作中的业户
             * 从histories表中取注册号，再到Corps表中查企业名称
             */
            case (strstr($keyword,'当前') or strstr($keyword,'dq')):
            try {
                $history = ManHistory::findOrFail($message['FromUserName']);
                $history_registration_num = $history->current_manipulating_corporation;
                $special_action_item = SpecialAction::where('registration_num', $history_registration_num)
                ->first();
                $history_corporation_name = $special_action_item->corp()->corporation_name;
                $special_action_name = $special_action_item->sp_name;
                $special_action_num = $special_action_item->sp_num;
                $special_action_corp_id = $special_action_item->sp_corp_id;
                $content= sprintf("目前操作企业为:\n代号为%s的”%s“行动的第%s号企业：\n%s\n%s", 
                                    $special_action_num, 
                                    $special_action_name, 
                                    $special_action_corp_id, 
                                    $history_corporation_name, 
                                    $history_registration_num);
                return $content;
                break;    
            } catch (ModelNotFoundException $e) {
                return '当前无指定操作企业';
                break;
            }

            /*
             * 收到“查询”之后
             * 回复当前操作中的业户的详情
             * 从histories表中取注册号，再到Corps表中取出详情$corp_to_be_search
             * 然后用fetch_corp_info()格式化返回详情信息
             */
            case($keyword == "查询" or $keyword == 'cx'):
            try {
                $history = ManHistory::findOrFail($message['FromUserName']);
                $history_registration_num = $history->current_manipulating_corporation;
                $corp_to_be_search = Corps::where('registration_num', (string)$history_registration_num)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return '查询当前操作用户失败';
                break;
            }
            return $this->fetch_corp_info($corp_to_be_search);
            break;

            /*
            以下处理44开头的注册号，或者指定关键词开头的代号
            第一个TRY尝试确定是否能在CORPS表中找到企业记录，如果找不到，即产生ModelNotFoundException，catch本异常后直接返回信息；
            第地个TRY尝试在UserManipulationHistories表中进行记录，以便进行上下文对话。
            注意：UserManipulationHistories表为复数名称，对应单数名称的UserManipulationHistory的Model类。
            */
            case(strstr($keyword,"44") AND strlen($keyword)>="6" OR preg_match('/^内资*/',$keyword) OR preg_match('/^独资*/',$keyword)):

            try {
                $corp_to_be_search = Corps::where('registration_num', (string)$keyword)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return sprintf('无法找到代号为 %s 的业户', $keyword);
                break;
            }
            try {
                $current_user = ManHistory::firstOrNew([
                    'id' => $message['FromUserName']
                ]);
                $current_user->previous_manipulated_corporation = $current_user->current_manipulating_corporation ?? '';
                $current_user->current_manipulating_corporation = $keyword;
                $current_user->save();
            } catch (\Exception $e) {
                return '保存到ManHistories时出错';
                break;    
            }

            return $this->fetch_corp_info($corp_to_be_search);

            // TODO 回复现场照片页面
            case(preg_match('/^现场|xc/',$keyword)):
            try {
                $history = ManHistory::findOrFail($message['FromUserName']);
                $history_registration_num = $history->current_manipulating_corporation;
                $corp_to_be_search = Corps::where('registration_num', (string)$history_registration_num)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return '查询当前操作用户失败';
                break;
            }

            $title = '企业信息与现场照片';
            $url = route('corp_photos.show', ['corporation_name' => $corp_to_be_search->corporation_name, 'user_openid' => $message['FromUserName']]);
            $image = 'http://sinacloud.net/aicbucket/babb0823bf2de393cbb694b1c7a71964.jpg';

            $items = [
                new NewsItem([
                    'title'       => $title,
                    'description' => "企业信息与现场照片（如有）",
                    'url'         => $url,
                    'image'       => $image,
                ]),
            ];
            $on_spot_photos = new News($items);
            return $on_spot_photos;
            break;

            //回复导航链接
            case(preg_match('/^导航*/',$keyword)):
            try {
                $history = ManHistory::findOrFail($message['FromUserName']);
                $history_registration_num = $history->current_manipulating_corporation;
            } catch (ModelNotFoundException $e) {
                return '查询当前操作用户失败';
                break;
            }
            return $this->get_corporation_route_plan($history_registration_num);
            break;

            // 添加备注
            case(preg_match('/^备注|bz*/',$keyword)):
            try {
                $history = ManHistory::findOrFail($message['FromUserName']);
                $history_registration_num = $history->current_manipulating_corporation;
            } catch (ModelNotFoundException $e) {
                return '查询当前操作用户失败';
                break;
            }
            $keyword = preg_replace('/^备注|bz+[ ：:,，]*/', '', $keyword);
            return $this->add_new_inspection_status($history_registration_num, $keyword);
            break;


            // 根据法人模糊查询
            case(preg_match('/^法人*/',$keyword)):
            $address = preg_replace('/^法人+[ ：:,，]*/', '', $keyword);
            $result = $this->get_corporation_info_by_keyword($address, 'rep_person');
            return $result;
            break;

            // 根据地址模糊查询
            case(preg_match('/^地址*/',$keyword)):
            $address = preg_replace('/^地址+[ ：:,，]*/', '', $keyword);
            $result = $this->get_corporation_info_by_keyword($address, 'address');
            return $result;
            break;

            // 模糊查询企业字号
            case(preg_match('~[\x{4e00}-\x{9fa5}]+~u', $keyword)):
            
            $result = $this->get_corporation_info_by_keyword($keyword, 'corp_name');
            return $result;
            break;

            default:
            return "功能列表：\n1.输入完整注册号指定要操作的企业，进行后续操作。\n2.输入字号模糊查询企业。\n3.输入“进入”，点击链接进入平台。";
            break;
        }
    }
    
    public function handle_image_message_special_action($message)
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
        $full_key = 'CorpImg/' . $current_corporation->corporation_aic_division . '/日常监管/' . date('Ymd'). '/' . $image_upload_name;  
        try {
            $result = $this->upload_image($full_key, $message['PicUrl']);
        } catch (\Exception $e) {
            return 'upload fail';
        }

        // $upload_image_link = 'https://aic-1253948304.cosgz.myqcloud.com/'. $full_key;
        // TODO 转用LARAVEL 一对多的关系
        CorpPhotos::create([
            'corporation_name' => $current_corporation->corporation_name,
            'link' => $full_key,
            'uploader' => $message['FromUserName'],
            'division' => $current_corporation->corporation_aic_division
        ]);
        $photos_number = CorpPhotos::where('corporation_name', $current_corporation->corporation_name)->count();
        Corps::find($history_registration_num)->update(['photos_number' => $photos_number]);
        return '成功上传照片，当前共有' . $photos_number . '张照片';
    }

    public function get_corporation_info_by_keyword_special_action($keyword, $type='corp_name')
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
        $corps_found = Corps::where($column, 'like', '%' .$keyword .'%')->take(10)->get();
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

    public function add_new_inspection_status_special_action($registration_num, $keyword)
    {
        try {
            $current_corporation = Corps::findOrFail($registration_num);
        } catch (ModelNotFoundException $e) {
            return '在数据库中无法找到当前操作企业，请重新指定要操作的企业。';
        }
        $today = \Carbon\Carbon::now()->format('Y-m-d');
        $old_inspection_status = $current_corporation->inspection_status;
        $new_inspection_status = $old_inspection_status . ';' . $today. ':'. $keyword;
        $current_corporation->inspection_status = $new_inspection_status;
        $current_corporation->save();

        return '当前的备注信息为：'. $current_corporation->inspection_status;
    }
}