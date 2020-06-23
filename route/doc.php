<?php

use think\facade\Route;

Route::group('doc', function () {
    Route::get('api', 'upload');
    Route::get('upload', 'upload');
    Route::get('imagelist', 'imageList');
    Route::get('folderlist', 'folderList');
    Route::get('imageinfo', 'imageInfo');
    Route::get('imageupdate', 'imageUpdate');
})->prefix('index.doc/');
