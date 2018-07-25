<?php

namespace App\Http\Controllers;

use App\CorpPhotos;
use App\Corps;
use App\User;

use Qcloud\Cos\Client;

use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class CorpPhotoController extends Controller
{

    /**
     * Display the specified resource.
     *
     * @param  \App\CorpPhotos  $corpPhotos
     * @return \Illuminate\Http\Response
     */
    public function show($corporation_name, $user_openid, Request $request, CorpPhotos $corpPhotos, Corps $corps, User $user, Client $cos_client)
    {
        try {
            $user->where('slaic_openid', $user_openid)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            abort(404);
        }
        $photo_items = $corpPhotos->where('corporation_name', $corporation_name)->get();
        $signed_url_list = array();
        foreach ($photo_items as $photo_item) {
            $url = "/{$photo_item->link}";
            $request = $cos_client->get($url);
            $signed_url = $cos_client->getObjectUrl(config('qcloud.bucket'), $photo_item->link, '+10 minutes');
            $signed_url = str_replace('http', 'https', $signed_url);
            $signed_url_list[$photo_item->id] = $signed_url;
        }

        $corp = $corps->where('corporation_name', $corporation_name)->first();
        // $request->user;
        return view('wechat_dialog.show_photos', compact('corp', 'photo_items', 'request', 'signed_url_list', 'user_openid'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CorpPhotos  $corpPhotos
     * @return \Illuminate\Http\Response
     */
    public function destroy(CorpPhotos $corpPhotos, Client $cos_client)
    {
        // 这里的$corp_photos已经由app/Providers/RouteServiceProvider.php里面的model和bind方法进行了参数绑定，因此不需要再进行find
        $key = str_replace('https://aic-1253948304.cosgz.myqcloud.com/', '', $corpPhotos->link);
        $key = urldecode($key);
        $result = $cos_client->deleteObject(array(
            //bucket 的命名规则为{name}-{appid} ，此处填写的存储桶名称必须为此格式
            'Bucket' => config('qcloud.bucket'),
            'Key' => $key));
        $corpPhotos->delete();
        $photos_number = CorpPhotos::where('corporation_name', $corpPhotos->corporation_name)->count();
        Corps::where('corporation_name', $corpPhotos->corporation_name)->first()->update(['photos_number' => $photos_number]);
        session()->flash('success', '成功删除照片');
        return redirect()->back();
    }
}
