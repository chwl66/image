<?php

use think\facade\Route;

//中心

Route::get('image/:signatures', 'index.image/index')
    ->pattern(['signatures' => '[\w]+']);

Route::group('/', function () {
    Route::get('/', 'index');
    Route::get('info/:signatures', 'info')
        ->pattern(['signatures' => '[\w]+']);;
    Route::get('about', 'about');
    Route::get('changelog', 'changelog');
    Route::get('explore', 'explore');
    Route::get('simple', 'simple');
})->prefix('index.index/');

Route::any('install', 'index.Install/index');



