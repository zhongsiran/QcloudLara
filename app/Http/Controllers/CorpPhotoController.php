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
    public function show(Request $request, CorpPhotos $corpPhotos, Corps $corps, User $user)
    {
        $photo_items = $corpPhotos->where('corporation_name', $request->corporation_name)->get();
        $corp = $corps->where('corporation_name', $request->corporation_name)->first();
        // $request->user;
        return view('wechat_dialog.show_photos', compact('corp', 'photo_items', 'request'));
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
        return redirect()->back();
    }
}
