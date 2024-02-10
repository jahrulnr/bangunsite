<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileManagerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WebsiteManagerController;
use Illuminate\Support\Facades\Route;

Route::middleware('basic.auth')->group(function () {
    Route::get('/', [AuthController::class, 'index'])->name('login');
    Route::post('/', [AuthController::class, 'auth'])->name('login.validate');
    Route::get('/locked', [AuthController::class, 'lockscreen'])->name('lockscreen');
});

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::resource('/website', WebsiteManagerController::class);
    Route::post('/website/{id}/updateConfig', [WebsiteManagerController::class, 'updateConfig'])->name('website.updateConfig');
    Route::post('/website/{id}/updateSSL', [WebsiteManagerController::class, 'updateSSL'])->name('website.updateSSL');

    Route::get('/browse', [FileManagerController::class, 'index'])->name('filemanager');
    Route::post('/browse/new', [FileManagerController::class, 'new'])->name('filemanager.new');

    Route::get('/phpinfo', function () {
        phpinfo();
    });
});
