<?php

use Illuminate\Http\Request;
use App\SpecialAction;
use App\Http\Resources\SpecialActionCollection;
use App\CorpPhotos;
use App\Http\Resources\CorpPhotoCollection;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
// 
Route::get('special_action/{division?}', function ($division = null, SpecialAction $special_action) {  #  默认可查全部
    // $special_action = new SpecialAction;
    $special_action_list = $special_action->index($division);

    return new SpecialActionCollection($special_action_list);
});

Route::get('photos/{division}/{action_num?}', function($division, $action_num = '', CorpPhotos $corpPhotos) {
    $photo_items = $corpPhotos->whereJsonContains('special_actions',  $action_num)->get();
    return new CorpPhotoCollection($photo_items);
});
