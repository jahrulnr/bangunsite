<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CronJobController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\DockerController;
use App\Http\Controllers\FileManagerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\MountManager;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SshController;
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
    Route::get('/website/{id}/installSSL', [WebsiteManagerController::class, 'installSSL'])->name('website.installSSL');
    Route::post('/website/{id}/updateSSL', [WebsiteManagerController::class, 'updateSSL'])->name('website.updateSSL');
    Route::patch('/website/updateNginx', [WebsiteManagerController::class, 'updateNginx'])->name('website.updateNginx');
    Route::resource('/website', WebsiteManagerController::class);
    Route::resource('/cronjob', CronJobController::class);
    Route::post('/cronjob/run/{id}', [CronJobController::class, 'run'])->name('cronjob.run')->where('id', '[0-9]+');
    Route::get('/database', [DatabaseController::class, 'index'])->name('database.index');
    Route::get('/database/{col}', [DatabaseController::class, 'show'])->name('database.show');
    Route::get('/setting', [SettingController::class, 'index'])->name('setting');
    Route::post('/setting/update/profile', [SettingController::class, 'updateProfile'])->name('setting.profile.update');
    Route::post('/setting/update/php', [SettingController::class, 'updatePHP'])->name('setting.php.update');
    Route::post('/setting/update/fpm', [SettingController::class, 'updateFPM'])->name('setting.fpm.update');
    Route::post('/setting/update/pool', [SettingController::class, 'updatePool'])->name('setting.pool.update');

    Route::get('/docker', [DockerController::class, 'index'])->name('docker.index');
    Route::get('/docker/restart/{id}', [DockerController::class, 'restart'])->name('docker.restart');
    Route::get('/docker/log/{id}', [DockerController::class, 'logs'])->name('docker.log');
    Route::get('/docker/stop/{id}', [DockerController::class, 'stop'])->name('docker.stop');

    Route::get('/browse', [FileManagerController::class, 'index'])->name('filemanager');
    Route::post('/browse/show', [FileManagerController::class, 'show'])->name('filemanager.showfile');
    Route::post('/browse/new', [FileManagerController::class, 'new'])->name('filemanager.new');
    Route::patch('/browse/action', [FileManagerController::class, 'action'])->name('filemanager.action');
    Route::delete('/browse/action', [FileManagerController::class, 'delete']);

    Route::get('/mount', [MountManager::class, 'index'])->name('mount');
    Route::post('/mount', [MountManager::class, 'add'])->name('mount.add');
    Route::post('/mount/update', [MountManager::class, 'update'])->name('mount.update');
    Route::get('/mount/enable', [MountManager::class, 'enable'])->name('mount.enable');
    Route::get('/mount/delete', [MountManager::class, 'destroy'])->name('mount.destroy');

    Route::get('/ssh', [SshController::class, 'index'])->name('ssh');
    Route::get('/ssh/connect/{id}', [SshController::class, 'connect'])->name('ssh.connect')->where('id', '[0-9]+');
    Route::post('/ssh', [SshController::class, 'add'])->name('ssh.add');
    Route::post('/ssh/update', [SshController::class, 'update'])->name('ssh.update');
    Route::get('/ssh/delete/{id}', [SshController::class, 'delete'])->name('ssh.delete')->where('id', '[0-9]+');

    Route::get('/logs', [LogController::class, 'index'])->name('logs');
    Route::get('/logs/get', [LogController::class, 'getLog'])->name('logs.get');

    Route::get('/phpinfo', function () {
        phpinfo();
    });
});

Route::any('/healty', function () {
    return env('APP_NAME').' run as expected '.PHP_EOL;
});
