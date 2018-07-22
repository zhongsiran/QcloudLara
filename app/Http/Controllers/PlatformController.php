<?php

namespace App\Http\Controllers;

use App\Corps;

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

    // 日常监管模块搜索页面
    public function daily()
    {
        return view('platform.daily');
    }


    /**
     * 日常监管模块搜索企业的逻辑
     * 
     *
     */
    public function daily_corp(Request $request, Corps $corp)
    {
        $result_corps = $corp->where('registration_num', 'like', '%'. $request->registration_num .'%')
                             ->where('corporation_name', 'like', '%'. $request->corporation_name .'%')
                             ->where('address', 'like', '%'. $request->address .'%')
                             ->where('represent_person', 'like', '%'. $request->represent_person .'%')
                             ->where('corporation_aic_division',  $request->corporation_aic_division)
                             ->paginate(15);
                             // ->get();
        return dump($result_corps);

        // return dump($request->all());
        // {"_token":"shhAiHZh1C6enTeAHEG2acSAwdJLOKcWg6NiQKxn","corporation_aic_division":"SL","registration_num":"1","corporation_name":"1","address":"1","represent_person":"1"}

    }

    public function special_action()
    {
        return view('platform.special_action');
    }
}
