<?php

namespace App\Http\Controllers;

use App\CorpPhotos;
use App\Corps;
use App\SpecialAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JavaScript;
use Qcloud\Cos\Client;

class PlatformController extends Controller
{
    // 登录和退出不需要登录，其他都需要
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['login', 'login_by_account_page', 'login_by_account', 'logout'],
        ]);
    }

    // 对应微信对话中提供的链接登录
    public function login(Request $request)
    {
        if (Auth::attempt(['slaic_openid' => $request->openid, 'password' => $request->openid, 'active_status' => true])) {
            return redirect()->route('platform.home');
        } else {
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
        } else {
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
    public function daily_search_form()
    {
        return view('platform.daily.search_form');
    }

    /**
     * 日常监管模块搜索企业的逻辑
     *
     *
     */
    public function daily_fetch_corp(Request $request, Corps $corp)
    {
        $result_corps = $corp->where('registration_num', 'like', '%' . $request->registration_num . '%')
            ->where('corporation_name', 'like', '%' . $request->corporation_name . '%')
            ->where('address', 'like', '%' . $request->address . '%')
            ->where('represent_person', 'like', '%' . $request->represent_person . '%')
            ->where('corporation_aic_division', $request->corporation_aic_division)
            ->paginate(8);
        $result_corps->withPath(url()->full());
        return view('platform.daily.result_list', compact('result_corps'));
    }

    public function daily_corp_detail($corporation_name, CorpPhotos $corpPhotos, Corps $corps, Client $cos_client)
    {
        $user_openid = Auth::user()->slaic_openid;
        $photo_items = $corpPhotos->where('corporation_name', $corporation_name)->get();
        foreach ($photo_items as $photo_item) {
            $url = "/{$photo_item->link}";
            $request = $cos_client->get($url);
            $signed_url = $cos_client->getObjectUrl(config('qcloud.bucket'), $photo_item->link, '+10 minutes');
            $signed_url = str_replace('http', 'https', $signed_url);
            $photo_item->signed_url = $signed_url;
        }

        $corp = $corps->where('corporation_name', $corporation_name)->first();
        JavaScript::put([
            'corp' => $corp,
            'photo_items' => $photo_items,
            'user_openid' => $user_openid
        ]);

        if (strstr(url()->previous(), '_token')){
            session(['daily_corp_list_url' => url()->previous()]);
        }

        $app = app('wechat.official_account');
        $jssdk_config = $app->jssdk->buildConfig(array('chooseImage', 'uploadImage', 'getLocation', 'openLocation'));
        $token = $app->access_token->getToken();

        return view('platform.daily.corp_detail', compact('corp','photo_items', 'user_openid', 'jssdk_config', 'token'));
    }

    public function special_action(SpecialAction $special_action)
    {
        $division = Auth::user()->user_aic_division;
        $special_action_list = $special_action->index($division);
        foreach ($special_action_list as $sp_item) {
            $sp_item->sp_count = $special_action->count($sp_item->sp_num);
            $sp_item->sp_finish_count = $special_action->finish_count($sp_item->sp_num);
        }

        return view('platform.special_action.index', compact('special_action_list'));
    }

    public function special_action_detail($sp_num, SpecialAction $special_action, Corps $corp)
    {

        $special_action_corps_list = $special_action->where('sp_num', $sp_num)
            ->where('sp_aic_division', Auth::user()->user_aic_division)
            ->orderBy('sp_corp_id')
        // ->get()
            ->paginate(10);
        // $special_action_corps_list->withPath(url()->full());
        $original_corp_list = $special_action->where('sp_num', $sp_num)
                  ->where('sp_aic_division', Auth::user()->user_aic_division)
                  ->get();
        $filtered_corp_list = $original_corp_list->mapWithKeys(function ($item) {
            return [$item['corporation_name'] => $item['sp_corp_id']];
        });

        JavaScript::put([
            'max_item' => $special_action_corps_list->total(),
            'corp_list' => $filtered_corp_list
        ]);

        foreach ($special_action_corps_list as $sp_item) {

            $corporation_infomation = $special_action->find($sp_item->id)->corp()->first();
            $sp_item->detail = $corporation_infomation;
        }
        return view('platform.special_action.corp_list', compact('special_action_corps_list'));
    }

    public function special_action_corp_detail($id, SpecialAction $special_action, Corps $corp, CorpPhotos $corpPhotos, Client $cos_client)
    {
        $sp_item = $special_action->find($id);
        $corp = $sp_item->corp()->first();

        $user_openid = Auth::user()->slaic_openid;
        $photo_items = $corpPhotos->where('corporation_name', $corp->corporation_name)
            ->whereJsonContains('special_actions', $sp_item->sp_num) // 根据行动名称过滤
            ->get();
        $signed_url_list = array();
        foreach ($photo_items as $photo_item) {
            $url = "/{$photo_item->link}";
            $request = $cos_client->get($url);
            $signed_url = $cos_client->getObjectUrl(config('qcloud.bucket'), $photo_item->link, '+10 minutes');
            $signed_url = str_replace('http', 'https', $signed_url);
            $photo_item->signed_url = $signed_url;
        }

        JavaScript::put([
            'sp_item' => $sp_item,
            'corp' => $corp,
            'photo_items' => $photo_items,
            'user_openid' => $user_openid
        ]);
        if (!strstr(url()->previous(), 'corps')){
            session(['corp_list_url' => url()->previous()]);
        }

        $app = app('wechat.official_account');
        $jssdk_config = $app->jssdk->buildConfig(array('chooseImage', 'uploadImage', 'getLocation', 'openLocation'));
        $token = $app->access_token->getToken();
        return View('platform.special_action.corp_detail', compact('corp', 'sp_item', 'photo_items',
                    'user_openid', 'jssdk_config', 'token'));
    }

}
