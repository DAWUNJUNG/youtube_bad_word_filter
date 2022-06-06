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
Route::put('/comment_update', 'YoutubeApiController@comment_update')->name('comment_update');
Route::put('/comment_del', 'YoutubeApiController@comment_del')->name('comment_del');
Route::put('/filter_update', 'YoutubeApiController@filter_update')->name('filter_update');
Route::put('/filter_del', 'YoutubeApiController@filter_del')->name('filter_del');
Route::put('/file_make', 'YoutubeApiController@file_make')->name('file_make');
Route::put('/file_del', 'YoutubeApiController@file_del')->name('file_del');
