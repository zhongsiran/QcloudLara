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
Route::get('special_action/{division?}', 'Apis\SpecialActionApiController@list_special_actions');

Route::get('photos/{division}/{action_num?}', 'Apis\SpecialActionApiController@fetch_photo_links');

Route::put('special_action/{id}', 'Apis\SpecialActionApiController@update_special_item');

Route::post('/corps_photos/', 'Apis\SpecialActionApiController@general_upload_photo');

Route::post('/corps/{registration_num}', 'Apis\SpecialActionApiController@update_corp');

Route::post('/special_action_set_finish_status/{id}', 'Apis\SpecialActionApiController@set_finish_status');