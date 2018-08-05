<?php

namespace App\Http\Controllers;

use App\User;
use App\UserManipulationHistory as ManHistory;
use App\Corps;
use App\Utils\WeChatAutoReplyTraits;
use App\Utils\WeChatMessageTypeSorterTraits;
use App\Utils\WeChatScanningTraits;
use App\Utils\WeChatSpecialActionTraits;

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
    protected $current_user;

    use WeChatAutoReplyTraits;
    use WeChatScanningTraits;
    use WeChatSpecialActionTraits;
    use WeChatMessageTypeSorterTraits;

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
        $this->current_user = User::firstOrCreate([
            "slaic_openid" => $message['FromUserName'],
        ]);
        $this->division = $this->current_user->user_aic_division;
        
        if ($this->current_user->active_status === true) {
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
                switch  ($this->current_user->mode) {
                    // 日常监管模式
                    case 'daily':
                    return $this->message_type_sorter_daily($message);
                    break;
                // 扫描模式
                    case 'scanning':
                    return $this->message_type_sorter_scanning($message);
                    break;
                // 专项行动模式
                    default:
                    return $this->message_type_sorter_special_action($message);
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
}
