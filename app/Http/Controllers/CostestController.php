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

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Log;

class CostestController extends Controller
{
    public function list(Client $client)
    {
        try {
            $result = $client->listObjects(array(
                'Bucket' => config('qcloud.bucket'),
                'Prefix' => 'CorpImg/SL/日常监管/广州华伟广告设计有限公司',
            ));
        // foreach ($result['Contents'] as $rt) {
        //     print_r($rt);
    // }
        } catch (\Exception $e) {
            return ($e);
        }
        $second = gettimeofday();
        // return $second['sec'].$second['usec'];
        // return $second;
        // return \Carbon\Carbon::now()->format('Y-m-d-s-v');
        return $result;
    }
}