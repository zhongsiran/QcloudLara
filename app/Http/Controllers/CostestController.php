<?php

namespace App\Http\Controllers;

use App\User;
use App\UserManipulationHistory as ManHistory;
use App\Corps;
use App\Utils\WeChatAutoReplyTraits;

use Illuminate\Http\Request;

use Qcloud\Cos\Api;
// use EasyWeChat\Kernel\Messages\Message;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Log;

class CostestController extends Controller
{
    public function list()
    {

        $cosClient = new \Qcloud\Cos\Client(array('region' => env('QCLOUD_REGION'),
            'credentials'=> array(
                'appId' => env('QCLOUD_APPID'),
                'secretId'    => env('QCLOUD_SECRETID'),
                'secretKey' => env('QCLOUD_SECRETKEY'))));
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
        return var_dump($cosClient);
    }
}