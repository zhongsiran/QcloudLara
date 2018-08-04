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
            case(preg_match('/^模式|ms|MS*/',$keyword)):
            $mode = preg_replace('/^模式|ms|MS+[ ：:,，]*/', '', $keyword);
            switch ($mode) {
                // 转成日常模式
                case '日常':
                $this->current_user->mode = 'daily';
                try {
                    $this->current_user->save();
                    ManHistory::clear($message['FromUserName']);
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
                    ManHistory::clear($message['FromUserName']);
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
                    ManHistory::clear($message['FromUserName']);
                    return '成功转换为专项行动模式，当前行动：' . $s->sp_num. ':' . $s->sp_name;
                } catch (\Exception $e) {
                    return '转换模式出错';
                };
                break;
            }
            break;

            // 列出专项行动
            case (strstr($keyword,'专项') or strstr($keyword, 'zx')):
            $actions_list = SpecialAction::index($this->current_user->user_aic_division);
            $actions_text = '';
            foreach ($actions_list as $action) {
                $actions_text  .= sprintf("行动序号：%s    行动名称：%s\n", $action->sp_num, $action->sp_name);
            }
            $actions_text = "提示：输入“模式+专项行动序号”切换到对应的专项行动（注意字母大小写）。\n" . $actions_text;
            return $actions_text;
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
                $special_action_item = SpecialAction::sp_item($this->current_user->mode, (string)$history_registration_num);
                $history_corporation_name = $special_action_item->corp()->firstOrFail()->corporation_name;
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
                $division = $this->current_user->user_aic_division;
                $sp_num = $this->current_user->mode;
                $sp_name = SpecialAction::sp_name($division, $sp_num);
                return sprintf("目前的专项行动是:\n代号为%s的”%s“行动，当前无指定操作企业",$sp_num, $sp_name);
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
                $sp_item = SpecialAction::sp_item($this->current_user->mode, (string)
                    $history_registration_num);
                $corp_item = $sp_item->corp()->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return '查询当前操作用户失败';
                break;
            }
            return $this->print_sp_item_info($corp_item, $sp_item);
            break;

            /*
            以下处理纯数字的内容，视为企业序号
            */
            case(is_numeric($keyword)):
            // return $keyword;
            // break;
            try {
                $sp_item_by_id = SpecialAction::sp_item_by_id($this->division, $this->current_user->mode, $keyword);
                $corp = $sp_item_by_id->corp()->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return sprintf('无法找到代号为 %s 的业户', $keyword);
                break;
            }
            try {
                $current_user = ManHistory::firstOrNew([
                    'id' => $message['FromUserName']
                ]);
                $current_user->previous_manipulated_corporation = $current_user->current_manipulating_corporation ?? '';
                $current_user->current_manipulating_corporation = $sp_item_by_id->registration_num;
                $current_user->save();
            } catch (\Exception $e) {
                return '保存到ManHistories时出错';
                break;    
            }
            return $this->print_sp_item_info($corp, $sp_item_by_id);
            break;

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
            case(preg_match('/^导航|dh|DH*/',$keyword)):
            try {
                $history = ManHistory::findOrFail($message['FromUserName']);
                $history_registration_num = $history->current_manipulating_corporation;
            } catch (ModelNotFoundException $e) {
                return '查询当前操作用户失败';
                break;
            }
            return $this->get_corporation_route_plan($history_registration_num);
            break;
            /* 以下是快速记录的关键词
             *查无
             *正常
             *停机
             *空号
             *不通
             */
            // 快速记录查无信息
            case(preg_match('/^查无|cw*/',$keyword)):
            try {
                $history = ManHistory::findOrFail($message['FromUserName']);
                $history_registration_num = $history->current_manipulating_corporation;
            } catch (ModelNotFoundException $e) {
                return '查询当前操作用户失败';
                break;
            }
            $sp_item = SpecialAction::sp_item($this->current_user->mode, (string)$history_registration_num);
            $corp = $sp_item->corp()->firstOrFail();
            $start_inspect_time = \Carbon\Carbon::now()->subMinute(15)->format('Y年m月d日H时i分');
            $end_inspect_time = \Carbon\Carbon::now()->addMinute(15)->format('Y年m月d日H时i分');
            $call_time = \Carbon\Carbon::now()->format('Y年m月d日H时i分');

            $sp_item->inspection_record = sprintf("执法人员在%s未发现当事人的经营迹象。当事人通过登记地址无法联系。", $corp->address);
            $sp_item->start_inspect_time = $start_inspect_time;
            $sp_item->end_inspect_time = $end_inspect_time;
            $sp_item->save();
            return sprintf("核查开始时间：%s\n核查结束时间：%s\n当前核查记录：%s", $sp_item->start_inspect_time, $sp_item->end_inspect_time, $sp_item->inspection_record);
            break;

            // 快速记录正常信息
            case(preg_match('/^正常|zc*/', $keyword)):
            try {
                $history = ManHistory::findOrFail($message['FromUserName']);
                $history_registration_num = $history->current_manipulating_corporation;
            } catch (ModelNotFoundException $e) {
                return '查询当前操作用户失败';
                break;
            }
            $sp_item = SpecialAction::sp_item($this->current_user->mode, (string)$history_registration_num);
            $corp = $sp_item->corp()->firstOrFail();
            $start_inspect_time = \Carbon\Carbon::now()->subMinute(15)->format('Y年m月d日H时i分');
            $end_inspect_time = \Carbon\Carbon::now()->addMinute(15)->format('Y年m月d日H时i分');
            $call_time = \Carbon\Carbon::now()->format('Y年m月d日H时i分');

            $sp_item->inspection_record = sprintf("当事人在%s正常经营", $corp->address);
            $sp_item->start_inspect_time = $start_inspect_time;
            $sp_item->end_inspect_time = $end_inspect_time;
            $sp_item->save();
            return sprintf("核查开始时间：%s\n核查结束时间：%s\n当前核查记录：%s", $sp_item->start_inspect_time, $sp_item->end_inspect_time, $sp_item->inspection_record);
            break;

            // 快速记录电话不通信息
            case(preg_match('/^停机|tj*/', $keyword)):
            try {
                $history = ManHistory::findOrFail($message['FromUserName']);
                $history_registration_num = $history->current_manipulating_corporation;
            } catch (ModelNotFoundException $e) {
                return '查询当前操作用户失败';
                break;
            }
            $sp_item = SpecialAction::sp_item($this->current_user->mode, (string)$history_registration_num);
            $corp = $sp_item->corp()->firstOrFail();
            $call_time = \Carbon\Carbon::now()->format('Y年m月d日H时i分');

            $sp_item->phone_call_record = sprintf("%s,执法人员拨打当事人的登记电话，该号码已经停机。", $call_time);
            $sp_item->save();
            return sprintf("当前电话记录：\n%s", $sp_item->phone_call_record);
            break;

            // 快速记录电话不通信息
            case(preg_match('/^空号|kh*/', $keyword)):
            try {
                $history = ManHistory::findOrFail($message['FromUserName']);
                $history_registration_num = $history->current_manipulating_corporation;
            } catch (ModelNotFoundException $e) {
                return '查询当前操作用户失败';
                break;
            }
            $sp_item = SpecialAction::sp_item($this->current_user->mode, (string)$history_registration_num);
            $corp = $sp_item->corp()->firstOrFail();
            $call_time = \Carbon\Carbon::now()->format('Y年m月d日H时i分');

            $sp_item->phone_call_record = sprintf("%s,执法人员拨打当事人的登记电话，该电话为空号。", $call_time);
            $sp_item->save();
            return sprintf("当前电话记录：\n%s", $sp_item->phone_call_record);
            break;

            // 快速记录电话无人接听信息
            case (preg_match('/^不通|bt*/', $keyword)):
            try {
                $history = ManHistory::findOrFail($message['FromUserName']);
                $history_registration_num = $history->current_manipulating_corporation;
            } catch (ModelNotFoundException $e) {
                return '查询当前操作用户失败';
                break;
            }
            $sp_item = SpecialAction::sp_item($this->current_user->mode, (string)$history_registration_num);
            $corp = $sp_item->corp()->firstOrFail();
            $call_time = \Carbon\Carbon::now()->format('Y年m月d日H时i分');

            $sp_item->phone_call_record = sprintf("%s,执法人员拨打当事人的登记电话，该电话无人接听。", $call_time);
            $sp_item->save();
            return sprintf("当前电话记录：\n%s", $sp_item->phone_call_record);
            break;

            case (preg_match('/^核查|hc*/', $keyword)):
            $keyword = preg_replace('/^核查|hc+[ ：:,，]*/', '', $keyword);
            return $this->add_new_inspection_status_special_action($message, $keyword);
            break;

            case (preg_match('/^电联|dl*/', $keyword)):
            $keyword = preg_replace('/^电联|dl+[ ：:,，]*/', '', $keyword);
            return $this->add_new_phone_record_special_action($message, $keyword);
            break;

            // 模糊查询企业字号
            case(preg_match('~[\x{4e00}-\x{9fa5}]+~u', $keyword)):
            
            $result = $this->get_corporation_info_by_keyword_special_action($keyword, 'corp_name');
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
            // case 'address':
            // $column = 'address';
            // $result_string = sprintf("地址包含'%s'的企业:\n", $keyword);
            // break;
            // case 'rep_person':
            // $column = 'represent_person';
            // $result_string = sprintf("法人包含'%s'的企业:\n", $keyword);
            // break;
            
            default:
            $column = 'corporation_name';
            $result_string = sprintf("名称包含'%s'的企业:\n", $keyword);
            break;
        }
        $sp_items = SpecialAction::where($column, 'like', '%' .$keyword .'%')
                                    ->where('sp_num', $this->current_user->mode)
                                    ->take(15)
                                    ->orderBy('sp_corp_id')
                                    ->get();
        $count = 1;
        if ($sp_items->count() > 0) {
            foreach ($sp_items as $sp_item) {
                $result_string .= 
                $count . ":\n" .
                '第' . $sp_item->sp_corp_id. "号\n".
                $sp_item->corporation_name . "\n" . 
                $sp_item->registration_num . "\n".
                "------------------------". "\n";
                $count += 1;
            }
            return $result_string;
        }else{
            return '无法找到名称/地址中包含“' . $keyword .'”的企业';
        }
    }

    public function add_new_inspection_status_special_action($message, $keyword)
    {
        try {
            $history = ManHistory::findOrFail($message['FromUserName']);
            $history_registration_num = $history->current_manipulating_corporation;
        } catch (ModelNotFoundException $e) {
            return '查询当前操作用户失败';
        }
        $sp_item = SpecialAction::sp_item($this->current_user->mode, (string)$history_registration_num);
        $corp = $sp_item->corp()->firstOrFail();
        $old_inpection_record = $sp_item->inspection_record;
        $start_inspect_time = \Carbon\Carbon::now()->subMinute(15)->format('Y年m月d日H时i分');
        $end_inspect_time = \Carbon\Carbon::now()->addMinute(15)->format('Y年m月d日H时i分');
        // $call_time = \Carbon\Carbon::now()->format('Y年m月d日H时i分');
        
        $sp_item->inspection_record = sprintf("%s%s", $old_inpection_record, $keyword);
        $sp_item->start_inspect_time = $sp_item->start_inspect_time ?? $start_inspect_time;
        $sp_item->end_inspect_time = $sp_item->end_inspect_time ?? $end_inspect_time;
        $sp_item->save();
        return sprintf("核查开始时间：%s\n核查结束时间：%s\n当前核查记录：%s", $sp_item->start_inspect_time, $sp_item->end_inspect_time, $sp_item->inspection_record);
    }

    public function add_new_phone_record_special_action($message, $keyword)
    {
        try {
            $history = ManHistory::findOrFail($message['FromUserName']);
            $history_registration_num = $history->current_manipulating_corporation;
        } catch (ModelNotFoundException $e) {
            return '查询当前操作用户失败';
        }
        $sp_item = SpecialAction::sp_item($this->current_user->mode, (string)$history_registration_num);
        $corp = $sp_item->corp()->firstOrFail();
        $old_phone_call_record = $sp_item->phone_call_record;
        // $call_time = \Carbon\Carbon::now()->format('Y年m月d日H时i分');
        
        $sp_item->phone_call_record = sprintf("%s%s", $old_phone_call_record, $keyword);
        $sp_item->save();
        return sprintf("最新电话联系记录：%s", $sp_item->phone_call_record);
    }

    public function print_sp_item_info(Corps $corp, SpecialAction $sp_item)
    {
        //用sprintf会保留换行和空格，为了代码易读，在书写时保持缩进，用str_replace将空格删除。不需要\n换行
        $corp_info_template = str_replace(' ','','
            %s
            %s
            所属专项行动：%s
            行动内序号：%s
            地址：%s
            法人：%s
            电话：%s
            联络员：%s
            联络员电话：%s
            日常核查记录：%s
            电话联系记录：%s
            专项核查记录：%s
            专项电话记录：%s
            图片数：%s
            ====================');
        return sprintf($corp_info_template,  // 模板
                //数据
            $corp->registration_num, 
            $corp->corporation_name,
            $sp_item->sp_name,
            $sp_item->sp_corp_id,
            $corp->address,
            $corp->represent_person,
            $corp->phone,
            $corp->contact_person,
            $corp->contact_phone,
            $corp->inspection_status,
            $corp->phone_call_record,
            $sp_item->inspection_record,
            $sp_item->phone_call_record,
            $corp->photos_number
        );
    }
}