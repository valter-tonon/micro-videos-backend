<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1'], function(){
    $except = ['except' => ['create','edit']];

    Route::resource('categories', 'CategoryController', $except);
    Route::resource('genres', 'GenreController', $except);
    Route::resource('cast_members', 'CastMemberController', $except);
    Route::resource('videos', 'VideoController', $except);
});
