<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PlatformController extends Controller
{
    // 登录和退出不需要登录，其他都需要
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['login', 'login_by_account_page', 'login_by_account', 'logout']
        ]);
    }

    // 对应微信对话中提供的链接登录
    public function login(Request $request)
    {
        if (Auth::attempt(['slaic_openid' => $request->openid, 'password' => $request->openid, 'active_status' => true])) {
            return redirect()->route('platform.home');
        }else{
            // abort(404);

            return view('platform.login_by_account_page', ['err_msg' => '登录失败，请重试']);
        }
    }

    // 返回网页版登录的页面
    public function login_by_account_page()
    {
        return view('platform.login_by_account_page');
    }

    // 处理网页版登录的POST
    public function login_by_account(Request $request)
    {
        if (Auth::attempt(['user_real_name' => $request->user_real_name, 'password' => $request->password, 'active_status' => true])) {
            return redirect()->route('platform.home');
        }else{
            // abort(404);
            return view('platform.login_by_account_page', ['err_msg' => '登录失败，请重试']);
        }
    }

    // 处理退出登录的请求
    public function logout()
    {
        Auth::logout();
        return redirect()->route('platform.login_by_account_page');
    }

    public function home()
    {
        return view('platform.home');
    }

    public function daily()
    {
        return view('platform.daily');
    }


    /*
     *
     *
     *
     */
    public function daily_corp(Request $request)
    {
        
        // return dump($request->all());  array

    }

    public function special_action()
    {
        return view('platform.special_action');
    }
}
