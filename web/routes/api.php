<?php

use App\Http\Controllers\HomeController;
use App\Libraries\Disk;
use App\Libraries\Network;
use Illuminate\Support\Facades\Route;

Route::post('/server/info', [HomeController::class, 'info'])->name('server.info');
Route::post('/server/traffic', [Network::class, 'traffic'])->name('server.traffic');
Route::post('/server/diskIO', [Disk::class, 'simpleStat'])->name('server.diskIO');
