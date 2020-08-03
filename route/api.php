<?php

use app\middleware\ApiFilter;
use think\facade\Route;

Route::group('api', function () {
    Route::any('upload', 'upload/upload');
    Route::any('imageUpdate', 'ImageUpdate/update');
    Route::any('folderList', 'FolderList/get');
    Route::any('imageList', 'ImageList/get');
    Route::any('imageInfo', 'ImageInfo/get');
    Route::any('qrcode', 'Qrcode/get');
})->prefix('api.')
    ->middleware(ApiFilter::class)
    ->allowCrossDomain();