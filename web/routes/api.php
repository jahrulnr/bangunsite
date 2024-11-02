<?php

use App\Http\Controllers\Api\ServerController;
use App\Http\Controllers\Api\SshController;
use Illuminate\Support\Facades\Route;

Route::post('/server/info', [ServerController::class, 'info'])->name('server.info');
Route::post('/server/traffic', [ServerController::class, 'traffic'])->name('server.traffic');
Route::post('/server/diskIO', [ServerController::class, 'disk'])->name('server.diskIO');

Route::post('/validateSSH', [SshController::class, 'validate'])->name('api.ssh.validate');
