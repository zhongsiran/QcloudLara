<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'StaticPageController@home');

Route::any('/wechat', 'WeChatController@serve');

Route::get('/corp_photos/{corporation_name}/user/{user_openid}', 'CorpPhotoController@show')->name('corp_photos.show');
Route::delete('/corp_photos/{corp_photo}', 'CorpPhotoController@destroy')->name('corp_photos.delete');

Route::any('login', 'PlatformController@login')->name('login');

Route::prefix('platform')->name('platform.')->group(function () {
    Route::get('/', 'PlatformController@home')->name('home');
    Route::any('login', 'PlatformController@login')->name('login');
    Route::any('logout', 'PlatformController@logout')->name('logout');
    Route::get('login_by_account', 'PlatformController@login_by_account_page')->name('login_by_account_page');
    Route::post('login_by_account', 'PlatformController@login_by_account')->name('login_by_account');

    Route::get('daily', 'PlatformController@daily_search_form')->name('daily_search_form');
    Route::get('daily/corps', 'PlatformController@daily_fetch_corp')->name('daily_fetch_corp');
    Route::get('daily/corps/{corporation_name}', 'PlatformController@daily_corp_detail')->name('daily_corp_detail');

    Route::get('special_action', 'PlatformController@special_action')->name('special_action');
    Route::get('special_action/{sp_num}', 'PlatformController@special_action_detail')->name('special_action_detail');
    Route::get('special_action/corps/{id}', 'PlatformController@special_action_corp_detail')->name('special_action.corp_detail');
});

