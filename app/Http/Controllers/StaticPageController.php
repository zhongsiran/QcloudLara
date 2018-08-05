<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaticPageController extends Controller
{
    // 静态的首页，不承担业务作用
    public function home()
    {
        return view('welcome');
    }

    public function jssdk()
    {
        return env('WECHAT_JSSDK_VERIFY_CODE');
    }
}
