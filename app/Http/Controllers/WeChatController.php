<?php

namespace App\Http\Controllers;

use App\User;
use App\UserManipulationHistory as ManHistory;
use App\Corps;

use Illuminate\Http\Request;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Log;

class WeChatController extends Controller
{

	public function __construct(User $user, ManHistory $history, Corps $corps)
	{
        $this->user = $user;
        $this->history = $history;
        $this->corps = $corps;
    }

    /**
     * 根据测试号的OPENID来判断是不是有授权对话的用户，如果是首次访问，则建立openid记录
     *
     * @param  EasyWeChat\Kernel\Messages\ $message
     *
     * @return boolean
     */

    private function authorize_with_slaic_openid($message)
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
                    return $message['FromUserName']. $current_user->user_name;
                    break;

                    case 'voice':
                    return '收到语音消息';
                    break;

                    case 'video':
                    return '收到视频消息';
                    break;

                    case 'location':
                    return '收到坐标消息';
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
                        ['user_real_name' => $message['Content']]
                    );
                }
                $current_user_real_name = User::where('slaic_openid', $message['FromUserName'])->first()->user_real_name ?? '无记录';
                return sprintf('你的微信号还未得到授权，请输入你的姓名进行记录,当前记录为“%s”（可多次输入,以最后一次为准）。然后联系管理员开通授权', $current_user_real_name);
            }
        });

        return $app->server->serve();
        // return var_dump($incoming_msg);
    }

    private function handle_text_message($message)
    {
    	$keyword = trim($message['Content']);
    	switch (true) {
    		case (strstr($keyword,'进入')):
    		$title = '微信监管平台' . session()->getId();
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

    		case (strstr($keyword,'当前')):
            $content="目前操作企业为：\n". session('corpname')."（".session('regnum')."）";
            return $content;
            break;

            case($keyword== "查询"):
            // $se_regnum = session('regnum');
            // $content .= $CurrentCorp->SearchCorpInfoByregnum($se_regnum);
            return '查询';
            break;

            case(strstr($keyword,"4401") AND strlen($keyword)>="10" OR preg_match('/^内资*/',$keyword) OR preg_match('/^独资*/',$keyword)):
            try {
                $corp_to_be_search = Corps::where('registration_num', (string)$keyword)->firstOrFail();
                return sprintf('找到代号为 %s 的业户', $keyword);
                break;
            } catch (ModelNotFoundException $e) {
                return sprintf('无法找到代号为 %s 的业户', $keyword);
                break;
            }
            
            case(preg_match('/^现场*/',$keyword)):
            return sprintf('查看 %s 的现场照片（如有）', $keyword);
            break;

            case(preg_match('/^导航*/',$keyword)):
                // $corp_desc = session('regnum'] . "\n" . $_SESSION['Addr');
                // $eword = session('corpname');
                // $epointx=session('LocX');
                // $epointy=session('LocY');
                // $content[0] = ["Title" =>session('corpname') ."\n". $corp_desc,"Description"=> "点击显示导航路径","PicUrl"=>"http://shilingaic-aic.stor.sinaapp.com/W020140128563566202451.png","Url"=>"http://apis.map.qq.com/tools/routeplan/eword=". $eword ."&epointx=".$epointx."&epointy=".$epointy."?referer=wxbro&key=6GJBZ-WKHKD-VBT4V-POM3Q-K3DW7-BJBL3
                // "];
                // if (session('PicNum')>0){
                //     $content[1] = ["Title" =>"现场图片","Description"=> "点击查看大图","Url"=>"http://shilingaic.applinzi.com/index.php?regnum=" . session('regnum')];
                // };
            return sprintf('导航到 %s （如有）', $keyword);
            break;

            case(preg_match('~[\x{4e00}-\x{9fa5}]+~u', $keyword)):
            session()->put('test_name', $keyword);
            return sprintf('回复所有带有 %s 的业户名单', session('test_name'));
            break;

            default:
            return "功能列表：\n1.输入完整注册号指定要操作的企业，进行后续操作。\n2.输入字号模糊查询企业。\n3.输入“进入”，点击链接进入平台。";
            break;
        }

    }
}
