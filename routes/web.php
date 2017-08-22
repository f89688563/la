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
//     Route::resource('goods', 'WApi\GoodsController');
    Route::get('goods/hot', 'WApi\GoodsController@hot');
    Route::get('goods/rec', 'WApi\GoodsController@rec');
    Route::get('goods/lists', 'WApi\GoodsController@lists');
    Route::get('goods/info', 'WApi\GoodsController@info');
    Route::get('goods/extra', 'WApi\GoodsController@extra');
	Route::put('goods/praise', 'WApi\GoodsController@praise');
	
	// 用户
	Route::get('member/about', 'WApi\MemberController@about');
	Route::get('member/login', 'WApi\MemberController@login');
	Route::post('member/feedback', 'WApi\MemberController@feedback');
	Route::get('member/stores', 'WApi\MemberController@stores');
	Route::get('member/employs', 'WApi\MemberController@employs');
	Route::resource('member', 'WApi\MemberController');
	
	// 购物车
	Route::resource('car', 'WApi\CarController');
	
	// 订单
	Route::get('order/extra', 'WApi\OrderController@extra');
	Route::resource('order', 'WApi\OrderController');
	
	// 地址
	Route::resource('address', 'WApi\AddressController');
	
	// 打卡
	Route::resource('punch', 'WApi\PunchController', [ 'only'=>['show', 'store'] ]);
	
	// 活动
	Route::resource('activity', 'WApi\ActivityController');
	
	// 抽奖
	Route::post('gift/red', 'WApi\GiftController@red');
	Route::get('gift/record', 'WApi\GiftController@record');
	
	// 积分
	Route::resource('record', 'WApi\RecordController', [ 'only'=>['index'] ]);
});

Route::resource('photo', 'PhotoController');

Route::get('api/test', function() {
	$data = ['haha'];
	echo json_encode($data);
});

