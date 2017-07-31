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

Route::group(['prefix'=>'wapi'], function() {
    Route::resource('ad', 'WApi\AdController');
    Route::get('goods/hot', 'WApi\GoodsController@hot');
    Route::get('goods/rec', 'WApi\GoodsController@rec');
//     Route::get('Index/index', [ 'uses' => 'WApi\IndexController@index' ]);
});

Route::get('api/test', function() {
	$data = ['haha'];
	echo json_encode($data);
});

