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

Route::get('/', function () {
    return view('welcome');
});

Route::any('/wechat', 'WeChatController@serve');

Route::get('/corp_photos/{corporation_name}/user/{user_openid}', 'CorpPhotoController@show')->name('corp_photos.show');
Route::delete('/corp_photos/{corp_photo}', 'CorpPhotoController@destroy')->name('corp_photos.delete');

Route::prefix('platform')->name('platform.')->group(function () {
    Route::get('/', 'PlatformController@home')->name('home');
    Route::get('login', 'PlatformController@login')->name('login');
    Route::get('login', 'PlatformController@login')->name('login');
    Route::any('logout', 'PlatformController@logout')->name('logout');
    Route::post('login_by_account', 'PlatformController@login_by_account')->name('login_by_account');

    Route::get('daily', 'PlatformController@daily')->name('daily');
    Route::get('special_action', 'PlatformController@special_action')->name('special_action');
});

