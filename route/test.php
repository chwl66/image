<?php

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
    opcache_reset();
});