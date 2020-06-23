<?php

use think\facade\Route;

Route::group('user', function () {
    Route::get('loginOut', 'loginOut');
    Route::get('login', 'login');
    Route::get('register', 'register');
    Route::get('images', 'images');
    Route::get('forget', 'forget');
    Route::get('resetPassword', 'resetPassword');
    Route::get('cache', 'cache');
    Route::get('node', 'node');
    Route::get('storage', 'storage');
    Route::get('finance', 'finance');
    Route::get('watermark', 'watermark');
    Route::get('/', 'index');
})->prefix('index.user/');