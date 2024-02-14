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

    Route::post('/website/{id}/updateConfig', [WebsiteManagerController::class, 'updateConfig'])->name('website.updateConfig');
    Route::options('/website/{id}/enableSite', [WebsiteManagerController::class, 'enableSite'])->name('website.enableSite');
    Route::delete('/website/{id}/enableSite', [WebsiteManagerController::class, 'destroy'])->name('website.destroy');
    Route::post('/website/{id}/updateSSL', [WebsiteManagerController::class, 'updateSSL'])->name('website.updateSSL');
    Route::patch('/website/updateNginx', [WebsiteManagerController::class, 'updateNginx'])->name('website.updateNginx');
    Route::resource('/website', WebsiteManagerController::class);

    Route::get('/browse', [FileManagerController::class, 'index'])->name('filemanager');
    Route::post('/browse/show', [FileManagerController::class, 'show'])->name('filemanager.showfile');
    Route::post('/browse/new', [FileManagerController::class, 'new'])->name('filemanager.new');
    Route::patch('/browse/action', [FileManagerController::class, 'action'])->name('filemanager.action');
    Route::delete('/browse/action', [FileManagerController::class, 'delete']);

    Route::get('/phpinfo', function () {
        phpinfo();
    });
});

Route::any('/healty', function () {
    return env('APP_NAME').' run as expected '.PHP_EOL;
});
