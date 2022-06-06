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

Route::get('/', 'YoutubeApiController@index')->name('list');
Route::get('/video', 'YoutubeApiController@video')->name('video');
Route::get('/comment', 'YoutubeApiController@comment')->name('comment');
Route::get('/comment/setting', 'YoutubeApiController@comment_setting')->name('comment_setting');
Route::get('/download', 'YoutubeApiController@download')->name('download');
