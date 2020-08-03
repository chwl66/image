<?php

use app\model\User;
use think\facade\Cache;
use think\facade\Request;
use think\facade\Route;

//Route::get('test', function () {
//    $models = \app\model\Api::order('id', 'asc')->select();
//    $id = 1;
//    foreach ($models as $key => $value) {
//        $value->id = $id;
//        $value->name = strtoupper($value->key);
//        $value->save();
//        $id++;
//    }
//});

Route::get('test', function () {
    $arr = [
        'a' => '',
        'b' => '',
        'c' => '',
        'distribute' => '',
        'this' => '',
    ];
    $va = rising_subscript($arr, ['distribute', 'this']);
    var_dump($va);

});
Route::get('testtest', function () {
    dump(Request::server());
});