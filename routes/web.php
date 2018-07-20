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
Route::get('/costest', 'CostestController@list');
Route::get('/corp_photos/{corporation_name}/user/{user_openid}', 'CorpPhotoController@show')->name('show_photos');
Route::delete('/corp_photos/{id}', 'CorpPhotoController@destroy')->name('delete_photo');
