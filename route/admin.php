<?php

use app\middleware\AdminAuth;
use think\facade\Route;

$authPath = 'admin';
try {
    $authPath = hidove_config_get('system.other.authPath');
} catch (\Exception $e) {

}

if ($authPath != '' && $authPath != 'admin') {
    Route::any('admin', function () {
        throw new \think\exception\HttpException(404, '禁止访问');
    });
} else {
    $authPath = 'admin';
}

Route::group($authPath, function () {
    Route::get('/', 'index');
})->prefix('admin.Index/')
    ->middleware(AdminAuth::class);

Route::get($authPath . '/', 'admin.Index/index')
    ->middleware(AdminAuth::class);


Route::get($authPath . '/login', 'admin.Login/index');

Route::any($authPath . '/ajax/login', 'ajax.AdminAuth/login');
Route::post($authPath . '/ajax/sql', 'ajax.AdminSql/execute');

Route::group($authPath . '/update', function () {
    Route::get('/', 'index');
    Route::get('download', 'download');
    Route::get('updateSql', 'updateSql');
    Route::get('check', 'check');
})->prefix('admin.Update/')
    ->middleware(AdminAuth::class);

Route::any($authPath . '/ajax/email/test', 'ajax.Email/test')
    ->middleware(AdminAuth::class);

Route::get($authPath . '/ajax/rechargeCard/export', 'ajax.AdminRechargeCard/export')
    ->middleware(AdminAuth::class);

Route::post($authPath . '/ajax/adminImageEdit/upload', 'ajax.AdminImageEdit/upload')
    ->middleware(AdminAuth::class);

foreach ([
             'AdminImages' => 'images',
             'AdminApi' => 'api',
             'AdminBlackList' => 'blackList',
             'AdminStorage' => 'storage',
             'AdminRechargeCard' => 'rechargeCard',
             'AdminUser' => 'user',
             'AdminGroup' => 'group',
             'AdminMenu' => 'menu',
             'AdminTemplate' => 'template',
             'AdminSet' => 'set',
             'AdminSetGroup' => 'setGroup',
         ] as $key => $value) {
    Route::group($authPath . "/ajax/$value", function () {
        Route::any('get', 'get');
        Route::any('create', 'create');
        Route::any('update', 'update');
        Route::any('delete', 'delete');
    })->prefix("ajax.$key/")
        ->middleware(AdminAuth::class);
}

Route::group($authPath . "/ajax/cache", function () {
    Route::any('imageByUrl', 'imageByUrl');
    Route::any('imageBySignatures', 'imageBySignatures');
    Route::any('imageIsValid', 'imageIsValid');
    Route::any('allOfImage', 'allOfImage');
    Route::any('allOfConfig', 'allOfConfig');
    Route::any('opcache', 'opcache');
    Route::any('all', 'all');
    Route::any('ImageByUsername', 'ImageByUsername');
})->prefix("ajax.AdminCache/")
    ->middleware(AdminAuth::class);

Route::group($authPath . "/ajax/statistics", function () {
    Route::any('getWeeklyPictureUpload', 'getWeeklyPictureUpload');
    Route::any('getTodayUserPictureUpload', 'getTodayUserPictureUpload');
    Route::any('getWeekApiRequestInfo', 'getWeekApiRequestInfo');
    Route::any('getTodayPictureRequest', 'getTodayPictureRequest');
    Route::any('getTodayRefereRequest', 'getTodayRefereRequest');
    Route::any('getTotalRefereRequest', 'getTotalRefereRequest');
    Route::any('getTotalInformation', 'getTotalInformation');
})->prefix("ajax.AdminStatistics/")
    ->middleware(AdminAuth::class);


if (env('APP_MASTER_DOMAIN') !== request()->host() && request()->baseUrl() !== '/install') {

    Route::domain(request()->host(), function () {
        Route::get('image/:signatures', 'index.image/index')
            ->pattern(['signatures' => '[\w]+']);
        Route::any('/', 'index.index/cdn');
    });
}