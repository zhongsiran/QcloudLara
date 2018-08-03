<?php

namespace App\Http\Controllers;

use App\User;
use App\UserManipulationHistory as ManHistory;
use App\Corps;
use App\Utils\WeChatAutoReplyTraits;

use Illuminate\Http\Request;
use Qcloud\Cos\Client;

// use EasyWeChat\Kernel\Messages\Message;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Log;

class WeChatController extends Controller
{

    use WeChatAutoReplyTraits;

    public function __construct(User $user, ManHistory $history, Corps $corps, Client $cos_client)
    {
        $this->user = $user;
        $this->history = $history;
        $this->corps = $corps;
        $this->cos_client = $cos_client;
    }

    /**
     * 根据测试号的OPENID来判断是不是有授权对话的用户，如果是首次访问，则建立openid记录
     *
     * @param  EasyWeChat\Kernel\Messages\ $message
     *
     * @return boolean
     */

    private function authorize_with_slaic_openid(array $message)
    {
        $current_user = User::firstOrCreate([
            "slaic_openid" => $message['FromUserName'],
        ]);
        
        if ($current_user->active_status === true) {
            return true;
        }else {
            return false;
        }

    }

    public function serve()
    {
        Log::info('request arrived.'); # 注意：Log 为 Laravel 组件，所以它记的日志去 Laravel 日志看，而不是 EasyWeChat 日志

        $app = app('wechat.official_account');

        $app->server->push(function($message){

            if ($this->authorize_with_slaic_openid($message)) {  # 判断是否已经授权用户

                switch ($message['MsgType']) {
                    case 'event':
                    return '收到事件消息';
                    break;

                    case 'text':
                    $result = $this->handle_text_message($message);
                    return $result;
                    break;

                    case 'image':
      		        // return '收到图片消息';
                    return $this->handle_image_message($message);
                    break;

                    case 'voice':
                    return '收到语音消息';
                    break;

                    case 'video':
                    return '收到视频消息';
                    break;

                    case 'location':
                    $result = $this->handle_location_message($message);
                    return $result ?? '收到坐标消息';
                    break;

                    case 'link':
                    return '收到链接消息';
                    break;

                    case 'file':
                    return '收到文件消息';
                    break;
                    // ... 其它消息
                    default:
                    return '收到其它消息';
                    break;
                }
            }else{
                //对于未授权用户：更新其 user_real_name字段用于给管理员后台判断开通权限使用
                if (isset($message['Content'])) {
                    User::updateOrCreate(
                        ['slaic_openid' => $message['FromUserName']],
                        ['user_real_name' => $message['Content'], 
                         'password' => Hash::make($message['FromUserName'])
                        ]
                    );
                }
                $current_user_real_name = User::where('slaic_openid', $message['FromUserName'])->first()->user_real_name ?? '无记录';
                return sprintf('你的微信号还未得到授权，请输入你的姓名进行记录,当前记录为“%s”（可多次输入,以最后一次为准）。然后联系管理员开通授权', $current_user_real_name);
            }
        });

        return $app->server->serve();
        // return var_dump($incoming_msg);
    }

    private function handle_text_message(array $message)
    {
    	$keyword = trim($message['Content']);
    	switch (true) {

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
                $history_corporation_name = Corps::where('registration_num', $history_registration_num)
                ->first()
                ->corporation_name;
                $content= sprintf("目前操作企业为:\n%s\n%s", $history_corporation_name, $history_registration_num);
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

}
