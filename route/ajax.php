<?php

use think\facade\Route;

Route::group('ajax', function () {
    Route::get('explore', 'explore');
    Route::get('uploadOption', 'uploadOption');
    Route::get('refresh', 'refresh');
    Route::get('imagePreview', 'imagePreview');
})->prefix('ajax.index/');

Route::group('ajax/auth', function () {
    Route::any('login', 'login');
    Route::any('register', 'register');
    Route::any('forget', 'forget');
    Route::any('resetPassword', 'resetPassword');
})->prefix('ajax.UserAuth/');

Route::group('ajax/userImages', function () {
    Route::any('getApiInfo', 'getApiInfo');
    Route::any('imageSearch', 'imageSearch');
    Route::any('folders', 'folders');
    Route::any('images', 'imageSearch');
    Route::any('buildFolder', 'buildFolder');
    Route::any('moveImage', 'moveImage');
    Route::any('deleteImage', 'deleteImage');
    Route::any('deleteFolder', 'deleteFolder');
    Route::any('updateImageInfo', 'updateImageInfo');
    Route::any('folderRename', 'folderRename');
})->prefix('ajax.UserImages/')
    ->middleware(\app\middleware\UserAuth::class);

Route::group('ajax/user', function () {
    Route::any('get', 'get');
    Route::any('updateApiFolder', 'updateApiFolder');
    Route::any('update', 'update');
})->prefix('ajax.UserControl/')
    ->middleware(\app\middleware\UserAuth::class);

Route::group('ajax/userCache', function () {
    Route::any('refreshConfig', 'refreshConfig');
    Route::any('imageIsValid', 'imageIsValid');
    Route::any('refreshAll', 'refreshAll');
    Route::any('refresh', 'refresh');
})->prefix('ajax.userCache/')
    ->middleware(\app\middleware\UserAuth::class);

Route::group('ajax/userNode', function () {
    Route::any('update', 'update');
})->prefix('ajax.userNode/')
    ->middleware(\app\middleware\UserAuth::class);

Route::group('ajax/userWatermark', function () {
    Route::any('get', 'get');
    Route::any('update', 'update');
})->prefix('ajax.UserWatermark/')
    ->middleware(\app\middleware\UserAuth::class);

Route::group('ajax/userStorage', function () {
    Route::get('get', 'get');
    Route::any('update', 'update');
})->prefix('ajax.userStorage/')
    ->middleware(\app\middleware\UserAuth::class);

Route::group('ajax/userFinance', function () {
    Route::any('recharge', 'recharge');
    Route::any('updateGroup', 'updateGroup');
    Route::any('getGroupList', 'getGroupList');
})->prefix('ajax.userFinance/')
    ->middleware(\app\middleware\UserAuth::class);

Route::group('ajax/api', function () {
    Route::get('get', 'get');
})->prefix('ajax.api/');

if (env('APP_MASTER_DOMAIN') !== request()->host() && request()->baseUrl() !== '/install') {

    Route::domain(request()->host(), function () {
        Route::get('image/:signatures', 'index.image/index')
            ->pattern(['signatures' => '[\w]+']);
        Route::any('/', 'index.index/cdn');
    });
}
