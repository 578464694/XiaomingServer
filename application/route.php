<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
//Route::rule(‘路由表达式’,‘路由地址’,‘请求类型’,‘路由参数（数组）’,‘变量规则（数组）’);
// 路由访问三段式，模块名/控制器名/方法名
use think\Route;

//api 代表接口，:version 是版本号，例如：v1，v2，v3...
Route::get('api/:version/banner/:id', 'api/:version.Banner/getBanner');

Route::get('api/:version/theme', 'api/:version.Theme/getSimpleList');
Route::get('api/:version/theme/:id', 'api/:version.Theme/getComplexOne');



Route::group('api/:version/product', function () {
    Route::get('/by_category', 'api/:version.Product/getAllInCategory');
    Route::get('/:id', 'api/:version.Product/getOne', [], ['id' => '\d+']);
    Route::get('/recent', 'api/:version.Product/getRecent');
});

Route::get('api/:version/category/all', 'api/:version.Category/getAllCategories');

Route::post('api/:version/token/user', 'api/:version.Token/getToken');

Route::post('api/:version/address','api/:version.Address/createOrUpdateAddress');


Route::post('api/:version/order','api/:version.Order/placeOrder');//创建订单
Route::get('api/:version/order/summary','api/:version.Order/getSummaryByUser');//历史订单
Route::get('api/:version/order/:id','api/:version.Order/getDetail','',['id' => '\d+']);//历史订单

Route::post('api/:version/pay/pre_order','api/:version.Pay/preOrder');//创建订单
Route::post('api/:version/pay/notify','api/:version.Pay/receiveNotify');//微信支付回调