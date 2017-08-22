<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels great to relax.
|
*/

require __DIR__.'/../bootstrap/autoload.php';

/*
|--------------------------------------------------------------------------
| Turn On The Lights
|--------------------------------------------------------------------------
|
| We need to illuminate PHP development, so let us turn on the lights.
| This bootstraps the framework and gets it ready for use, then it
| will load up this application so that we can run it and send
| the responses back to the browser and delight our users.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the kernel, and send the associated response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/

// 配置名
define('CONFIG_KF', 'kf');
define('CONFIG_ABOUT', 'about');
define('CONFIG_GIFT', 'gift');
define('CONFIG_REG', 'reg');
define('CONFIG_PUNCH', 'punch');
define('CONFIG_SCALE', 'scale');
define('CONFIG_ADDRESS', 'address');
define('CONFIG_FARE', 'fare');
define('CONFIG_SHARE_EXPLAIN', 'share_explain');

// 微信相关
define('CONFIG_APPID', 'appid');
define('CONFIG_MCHID', 'mchid');
define('CONFIG_APPKEY', 'appkey');
define('CONFIG_APPSECRET', 'appsecret');
// 订单支付
define('CONFIG_MODULE_ORDER_PAY', 'orderpay');
// 会员加入
define('CONFIG_MODULE_MEMBER_IN', 'memberin');
// 订单信息提醒
define('CONFIG_MODULE_ORDER_CHANGE', 'orderchange');
// 积分变动
define('CONFIG_MODULE_JF_CHANGE', 'jfchange');

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
