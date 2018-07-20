<?php

namespace App\Http\Controllers;

use App\CorpPhotos;
use App\Corps;
use App\User;

use Qcloud\Cos\Client;

use Illuminate\Http\Request;

class CorpPhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CorpPhotos  $corpPhotos
     * @return \Illuminate\Http\Response
     */
    public function show($corporation_name, $user_openid, Request $request, CorpPhotos $corpPhotos, Corps $corps, User $user, Client $cos_client)
    {
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
     * Show the form for editing the specified resource.
     *
     * @param  \App\CorpPhotos  $corpPhotos
     * @return \Illuminate\Http\Response
     */
    public function edit(CorpPhotos $corpPhotos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CorpPhotos  $corpPhotos
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CorpPhotos $corpPhotos)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CorpPhotos  $corpPhotos
     * @return \Illuminate\Http\Response
     */
    public function destroy(CorpPhotos $corpPhotos, Client $cos_client, $id)
    {
        $corporation_photo = $corpPhotos->find($id);
        $key = str_replace('https://aic-1253948304.cosgz.myqcloud.com/', '', $corporation_photo->link);
        $key = urldecode($key);
        $result = $cos_client->deleteObject(array(
            //bucket 的命名规则为{name}-{appid} ，此处填写的存储桶名称必须为此格式
            'Bucket' => config('qcloud.bucket'),
            'Key' => $key));
        $corporation_photo->delete();
        session()->flash('success', '成功删除照片');
        return redirect()->back();
    }
}
