<?php


use think\facade\Route;


Route::group('cron', function () {
    Route::get('imageUpdate', 'ImageUpdate/index');
})->prefix('cron.');
