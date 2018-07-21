<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;

class PlatformController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['login', 'logout']
        ]);
    }

    public function login(Request $request)
    {
        if (Auth::attempt(['slaic_openid' => $request->openid, 'password' => $request->openid, 'active_status' => true])) {
            return redirect()->route('platform.home');
        }else{
            abort(403, '登陆失败，请联系管理员');
            // abort(404);
        }
    }

    public function login_by_account(Request $request)
    {
        if (Auth::attempt(['user_name' => $request->user_name, 'password' => $request->password, 'active_status' => true])) {
            return redirect()->route('platform.home');
        }else{
            abort(403, '登陆失败，请联系管理员');
            // abort(404);
        }
    }

    public function logout()
    {
        Auth::logout();
        if (Auth::check()) {
            return 'still login';
        }else{
            abort(403, '你已经退出登录');
        }
    }

    public function home()
    {
        return view('platform.home');
    }

    public function daily()
    {
        return view('platform.daily');
    }

    public function daily_corp(Request $request)
    {
        
        return dump($request->all());
    }

    public function special_action()
    {
        return view('platform.special_action');
    }
}
