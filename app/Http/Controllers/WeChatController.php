<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use Log;

class WeChatController extends Controller
{

	public function __construct(Text $texts, News $news, NewsItem $news_item)
	{
		$this->texts = $texts;
		$this->news = $news;
		$this->news_item = $news_item;
	}

	public function serve()
	{
        Log::info('request arrived.'); # 注意：Log 为 Laravel 组件，所以它记的日志去 Laravel 日志看，而不是 EasyWeChat 日志

        $app = app('wechat.official_account');

        $app->server->push(function($message){
        	switch ($message['MsgType']) {
        		case 'event':
        		return '收到事件消息';
        		break;

        		case 'text':
        		$result = $this->handle_text_message($message);
        		return $result;
        		break;

        		case 'image':
        		return '收到图片消息';
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
	        // ... 其它消息
        		default:
        		return '收到其它消息';
        		break;
        	}

        });

        return $app->server->serve();
        // $list = $app->material->list('image', 0, 10);
        // return var_dump($news_1);
    }

    private function handle_text_message($message)
    {
    	$keyword = trim($message['Content']);
    	switch (true) {
    		case (strstr($keyword,'进入')):
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
    		$news = new News($items);
    		return $news;
    		break;
    		
    		default:
    			return "请输入“进入”";
    		break;
    	}
    	
    }
}
